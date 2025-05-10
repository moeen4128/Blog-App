<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
     protected $fillable = ['title', 'content', 'user_id', 'status', 'featured_image','categories', 'tags'];
     protected $casts = ['categories' => 'array', 'tags' => 'array'];
     public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
