<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'post_id',
        'author_id',
    ];

    public function post()
    {
        return $this->belongsTo(\App\Models\Post::class);
    }

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
