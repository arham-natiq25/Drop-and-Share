<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Share extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'zip_filename',
        'total_size',
        'file_count',
        'download_count',
        'last_downloaded_at',
        'expires_at',
        'deleted_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'last_downloaded_at' => 'datetime',
            'expires_at' => 'datetime',
            'deleted_at' => 'datetime',
            'total_size' => 'integer',
            'file_count' => 'integer',
            'download_count' => 'integer',
        ];
    }

    public function files(): HasMany
    {
        return $this->hasMany(ShareFile::class);
    }

    /**
     * Absolute path to the zip archive on disk.
     */
    public function zipPath(): string
    {
        return config('dropnshare.zips_path').'/'.$this->zip_filename;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * The link a human shares: the frontend download page.
     */
    public function shareUrl(): string
    {
        return config('dropnshare.frontend_url').'/download/'.$this->zip_filename;
    }

    /**
     * The link that streams the actual bytes.
     */
    public function downloadUrl(): string
    {
        return url('api/download/'.$this->zip_filename);
    }

    /**
     * Remove the zip from disk and flag the row as purged.
     */
    public function purge(): void
    {
        $path = $this->zipPath();

        if (is_file($path)) {
            @unlink($path);
        }

        if ($this->deleted_at === null) {
            $this->forceFill(['deleted_at' => now()])->save();
        }
    }
}
