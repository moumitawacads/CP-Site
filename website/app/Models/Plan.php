<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    public function prices()
    {
        return $this->hasMany(PlanPrice::class, 'plan_id', 'id');
    }

    public function features()
    {
        return $this->hasOne(PlanFeature::class, 'plan_id', 'id');
    }
}
