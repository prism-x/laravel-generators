<?php

namespace PrismX\Generators\Tests;

use Illuminate\Support\Facades\File;
use PrismX\Generators\GeneratorServiceProvider;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    use MockeryPHPUnitIntegration;

    public function fixture(string $path)
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR));
    }
    public function fixturePath(string $path)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    protected function getPackageProviders($app)
    {
        return [GeneratorServiceProvider::class];
    }

    protected function parseDefinition($contents)
    {
        return preg_replace_callback('/^(\s+)(id|timestamps(Tz)?|softDeletes(Tz)?)$/mi', function ($matches) {
            return $matches[1] . strtolower($matches[2]) . ': ' . $matches[2];
        }, $contents);
    }
}
