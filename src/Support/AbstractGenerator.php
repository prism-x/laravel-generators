<?php

namespace PrismX\Generators\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

abstract class AbstractGenerator
{
    const INDENT = '            ';

    /* @var \PrismX\Generators\Support\Model */
    protected $model;

    protected $stub;

    abstract protected function getpath(): string;

    abstract public function populateStub(): string;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function setCache($key, $contents)
    {
        Storage::put('generators/cache/' . md5($key) . '.php', $contents);
    }

    public function getCache($key)
    {
        $file = 'generators/cache/' . md5($key) . '.php';

        return Storage::exists($file) ? Storage::get($file) : null;
    }

    public function run()
    {
        $parsedStub = $this->populateStub();
        $cached = $this->getCache($this->getPath());

        if (! $cached || ! File::exists($this->getpath())) {
            File::put($this->getPath(), $parsedStub);
            $this->setCache($this->getPath(), $parsedStub);

            return "  {$this->getType()} created successfully. <comment>[{$this->getPath()}]</comment>";
        }

        if (File::get($this->getPath()) === $cached) {
            File::put($this->getPath(), $parsedStub);
            $this->setCache($this->getPath(), $parsedStub);

            return "  {$this->getType()} updated successfully. <comment>[{$this->getPath()}]</comment>";
        }

        return "  {$this->getType()} was manually changed. <comment>[skipped]</comment>";
    }

    public function getType()
    {
        $class = (new \ReflectionClass($this))->getShortName();

        return str_replace('Generator', '', $class);
    }
}
