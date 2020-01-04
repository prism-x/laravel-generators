<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'published_at',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    protected $dates = [
        'published_at',
    ];
}
