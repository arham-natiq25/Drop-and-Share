<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'share_id',
        'original_name',
        'stored_name',
        'mime_type',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function share(): BelongsTo
    {
        return $this->belongsTo(Share::class);
    }
}
