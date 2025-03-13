<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentsLatestAction extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'parents_latest_actions';

    protected $fillable = [
        'parents_id',
        'tab_id',
        'action_description',
        'dynamic_value'
    ];

    protected $appends = [
        "json_decoded_dynamic_value",
        "lang_action_description"
    ];

    public function parents()
    {
        return $this->belongsTo(Parents::class, 'parents_id', 'id');
    }

    public function getJsonDecodedDynamicValueAttribute()
    {
        return json_decode($this->dynamic_value, true);
    }

    public function getLangActionDescriptionAttribute()
    {
        if (str_contains($this->action_description, 'now connected with')) {
            return "action.learnerConnected";
        } else if (str_contains($this->action_description, 'learner account created')) {
            return "action.learnerAccountCreated";
        } else if (str_contains($this->action_description, 'data has been updated')) {
            return "action.learnerUpdated";
        } else if (str_contains($this->action_description, 'service added for')) {
            return "action.learnerService";
        }
    }
}
