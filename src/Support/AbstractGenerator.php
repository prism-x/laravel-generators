<?php

namespace PrismX\Generators\Support;

abstract class AbstractGenerator
{
    const INDENT = '            ';

    protected $tree;

    public function __construct(array $tree)
    {
        $this->tree = $tree;
        $this->output();
    }
}
