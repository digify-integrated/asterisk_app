<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadSettingExtension extends Model
{
    protected $table = 'upload_setting_extensions';

    protected $fillable = [
        'upload_setting_id',
        'extension',
        'last_log_by',
    ];

    public function uploadSetting(): BelongsTo
    {
        return $this->belongsTo(UploadSetting::class, 'upload_setting_id');
    }
}
