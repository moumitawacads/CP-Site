<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnerSessionGame extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'learner_session_id',
        'game_name',
        'game_score',
        'number_of_attempts'
    ];

    public function learnerSession()
    {
        return $this->belongsTo(LearnerSession::class, 'learner_session_id', 'id');
    }

    public function learnerGameLevels()
    {
        return $this->hasMany(LearnerGameLevel::class, 'learner_session_game_id', 'id');
    }
}
