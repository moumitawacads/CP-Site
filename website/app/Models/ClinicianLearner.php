<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicianLearner extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'clinician_learner';

    protected $fillable = [
        'clinician_id',
        'learner_id',
        'learner_encrypt_data_id',
    ];

    public function clinicians()
    {
        return $this->belongsToMany(Clinician::class, 'clinician_id', 'id');
    }
}
