<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GdprAgree extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = "gdpr_agree";

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
