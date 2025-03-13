<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnerGameLevel extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'learner_session_game_id',
        'audio_file_path',
        'words',
        'two_words_phrase',
        'silly_sentence',
        'story',
        'answer_type'
    ];

    public function learnerSessionGame()
    {
        return $this->belongsTo(LearnerSessionGame::class, 'learner_session_game_id', 'id');
    }
}
