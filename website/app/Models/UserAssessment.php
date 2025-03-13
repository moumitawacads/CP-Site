<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssessment extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $fillable = [
        'user_id',
        'learner_id',
        'clinician_id',
        'learner_unique_code',
        'status',
        'scheduled_meeting_start_datetime',
        'scheduled_meeting_end_datetime',
        'scheduled_meeting_timezone',
        'assign_by',
        'calendar_webhook_response'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }

    public function clinician()
    {
        return $this->belongsTo(Clinician::class);
    }

    public function assignBy()
    {
        return $this->belongsTo(User::class, 'assign_by', 'id');
    }
}
