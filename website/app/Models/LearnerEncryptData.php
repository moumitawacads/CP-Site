<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnerEncryptData extends Model
{
    use HasFactory;

    protected $connection = "encrypted_mysql";
    protected $table = "learners_encrypt_data";
    protected $fillable = [
        "clinician_id",
        "speech_language_diagnosis",
        "first_name",
        "last_name",
        "clinician_name",
        "gender",
        "name_of_school",
        "grade",
        "culture",
        "family_diagnosis",
        "history",
        "learner_type",
        "learner_email",
    ];
}
