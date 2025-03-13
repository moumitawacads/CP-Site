<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsNew extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = "whats_new";
    protected $fillable = [
        'title',
        'content',
        'file_url',
        'author_id',
        'category_id',
        'status',
        'published_at',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
