<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $fillable = [
        'user_id',
        'device_name',
        'ip_address',
        'user_agent',
        'token',
        'last_active_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
