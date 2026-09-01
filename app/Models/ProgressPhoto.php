<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressPhoto extends Model
{
    protected $fillable = [
        'progress_update_id',
        'file_name',
        'file_path',
        'file_url',
        'caption',
        'uploaded_by',
    ];

    public function progressUpdate(): BelongsTo
    {
        return $this->belongsTo(ProgressUpdate::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
