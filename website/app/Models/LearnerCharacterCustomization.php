<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnerCharacterCustomization extends Model
{
    use HasFactory;
    protected $connection = 'mysql';

    protected $fillable = [
        'learner_id',
        'selected_bodycolor',
        'selected_eyecolor',
        'selected_eyebrow',
        'selected_hair',
        'selected_upperwear',
        'selected_pant',
        'selected_backpack',
        'selected_shoe',
        'selected_hat',
        'selected_glove',
        'selected_glasses'
    ];

    public function learner()
    {
        return $this->belongsTo(Learner::class, 'learner_id', 'id');
    }
}
