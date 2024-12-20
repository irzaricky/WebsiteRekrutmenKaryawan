<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatabaseBackup extends Model
{
    protected $fillable = [
        'filename',
        'path',
        'category',
        'type',
        'user_id',
        'size'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
