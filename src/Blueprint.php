<?php

namespace PrismX\Generators;

use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Support\Lexer;

class Blueprint
{
    protected $contents;

    public static function make(string $filename)
    {
        $instance = new self($filename);

        return (new Lexer())->analyze($instance->contents['models'] ?? []);
    }

    public function __construct(string $file)
    {
        $content = preg_replace_callback('/^(\s+)(id|timestamps(Tz)?|softDeletes(Tz)?)$/mi', function ($matches) {
            return $matches[1].strtolower($matches[2]).': '.$matches[2];
        }, File::get($file));

        $this->contents = Yaml::parse($content);
        $this->setConfig();
    }

    protected function setConfig()
    {
        $config = array_merge(config('generators'), $this->contents['options'] ?? []);
        config(['generators' => $config]);
    }
}
