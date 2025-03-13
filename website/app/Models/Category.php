<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $fillable = [
        'name',
    ];

    public function whatsNew()
    {
        return $this->hasMany(WhatsNew::class, 'category_id', 'id');
    }
}
