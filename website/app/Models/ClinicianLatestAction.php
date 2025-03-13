<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicianLatestAction extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'clinician_latest_actions';

    protected $fillable = [
        'clinician_id',
        'tab_id',
        'action_description',
        'dynamic_value'
    ];

    protected $appends = [
        "json_decoded_dynamic_value",
        "lang_action_description"
    ];

    public function clinician()
    {
        return $this->belongsTo(Clinician::class, 'clinician_id', 'id');
    }

    public function getJsonDecodedDynamicValueAttribute()
    {
        return json_decode($this->dynamic_value, true);
    }

    public function getLangActionDescriptionAttribute()
    {
        if (str_contains($this->action_description, 'system setting is enabled')) {
            return "action.systemSettingEnable";
        } else if (str_contains($this->action_description, 'system setting is disabled')) {
            return "action.systemSettingDisable";
        } else if (str_contains($this->action_description, 'Homework Helper enabled for')) {
            return "action.homeworkHelperEnabled";
        } else if (str_contains($this->action_description, 'Homework Helper disabled for')) {
            return "action.homeworkHelperDisabled";
        } else if (str_contains($this->action_description, 'learner account created')) {
            return "action.learnerAccountCreated";
        }
    }
}
