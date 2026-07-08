<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UploadSetting extends Model
{
    protected $table = 'upload_settings';

    protected $fillable = [
        'name',
        'max_file_size',
        'last_log_by',
    ];

    public function extensions(): HasMany
    {
        return $this->hasMany(UploadSettingExtension::class, 'upload_setting_id');
    }
}
