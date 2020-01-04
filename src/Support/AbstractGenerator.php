<?php

namespace PrismX\Generators\Support;

use Illuminate\Filesystem\Filesystem;

abstract class AbstractGenerator
{
    const INDENT = '            ';

    /* @var \PrismX\Generators\Support\Model */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;

    }
}
