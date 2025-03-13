<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Clinician;

class Occupation extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    public function clinicians()
    {
        return $this->hasMany(Clinician::class, 'occupation_id', 'id');
    }
}
