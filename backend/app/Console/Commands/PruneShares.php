<?php

namespace App\Console\Commands;

use App\Models\Share;
use Illuminate\Console\Command;

class PruneShares extends Command
{
    protected $signature = 'shares:prune {--dry-run : List what would be removed without deleting anything}';

    protected $description = 'Delete expired share archives from disk and mark their rows as purged';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $expired = Share::whereNull('deleted_at')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Nothing to prune.');
        }

        $bytes = 0;
        foreach ($expired as $share) {
            $path = $share->zipPath();
            $size = is_file($path) ? filesize($path) : 0;
            $bytes += $size;

            $this->line(($dryRun ? '[dry-run] ' : '').'Pruning '.$share->zip_filename." ({$size} bytes)");

            if (! $dryRun) {
                $share->purge();
            }
        }

        // Sweep any archive on disk that has no live row pointing at it, plus
        // temp folders left behind by an upload that died halfway through.
        $orphans = $this->pruneOrphans($dryRun);
        $this->pruneStaleTempDirs($dryRun);

        $this->info(sprintf(
            '%s %d expired share(s) and %d orphaned file(s), freeing %s.',
            $dryRun ? 'Would prune' : 'Pruned',
            $expired->count(),
            $orphans,
            $this->humanSize($bytes)
        ));

        return self::SUCCESS;
    }

    private function pruneOrphans(bool $dryRun): int
    {
        $zipsDir = config('dropnshare.zips_path');

        if (! is_dir($zipsDir)) {
            return 0;
        }

        $live = Share::whereNull('deleted_at')->pluck('zip_filename')->flip();
        $count = 0;

        foreach (glob($zipsDir.'/*.zip') ?: [] as $path) {
            if ($live->has(basename($path))) {
                continue;
            }

            $this->line(($dryRun ? '[dry-run] ' : '').'Removing orphan '.basename($path));

            if (! $dryRun) {
                @unlink($path);
            }

            $count++;
        }

        return $count;
    }

    private function pruneStaleTempDirs(bool $dryRun): void
    {
        $tempRoot = config('dropnshare.temp_path');

        if (! is_dir($tempRoot)) {
            return;
        }

        foreach (glob($tempRoot.'/*', GLOB_ONLYDIR) ?: [] as $dir) {
            // Anything still around after an hour is debris, not an upload.
            if (filemtime($dir) > now()->subHour()->getTimestamp()) {
                continue;
            }

            $this->line(($dryRun ? '[dry-run] ' : '').'Removing stale temp dir '.basename($dir));

            if (! $dryRun) {
                $this->deleteDirectory($dir);
            }
        }
    }

    private function deleteDirectory(string $dir): void
    {
        foreach (array_diff(scandir($dir) ?: [], ['.', '..']) as $entry) {
            $path = $dir.'/'.$entry;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }

        @rmdir($dir);
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 Bytes';
        }

        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return round($bytes / (1024 ** $power), 2).' '.$units[$power];
    }
}
