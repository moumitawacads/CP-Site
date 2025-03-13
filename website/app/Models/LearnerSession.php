<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnerSession extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        "learner_id",
        "session_start_time",
        "atrium",
        "world",
        "target_sound",
        "pick_possition",
        "pick_syllabus",
        "spell_word",
        "session_end_time",
    ];

    public function learner()
    {
        return $this->belongsTo(Learner::class, 'learner_id', 'id');
    }

    public function sessionGames()
    {
        return $this->hasMany(LearnerSessionGame::class, 'learner_session_id', 'id');
    }
}
