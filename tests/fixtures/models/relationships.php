<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'post_id',
        'author_id',
    ];

    public function post()
    {
        return $this->belongsTo(\App\Post::class);
    }

    public function author()
    {
        return $this->belongsTo(\App\User::class);
    }
}
