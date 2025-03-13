<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicianSetting extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'clinician_system_settings';

    protected $fillable = [
        'clinician_id',
        'ai_control_flag',
        'homework_helper_flag',
        'pre_set_videos_flag',
        'cues_flag',
        'prompts_flag',
        'zoom_mode_flag',
    ];

    public function clinician()
    {
        return $this->belongsTo(Clinician::class, 'clinician_id', 'id');
    }
}
