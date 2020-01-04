<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class Post extends Resource
{
    public static $model = \App\Post::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Title'),
            BelongsTo::make('Author'),
            Text::make('Content'),
            DateTime::make('Published At'),
            Number::make('Word Count'),
        ];
    }
}
