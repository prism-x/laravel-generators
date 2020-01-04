<?php

namespace PrismX\Generators;

use PrismX\Generators\Commands\Build;
use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Build::class,
            ]);
        }

        if (! defined('STUBS_PATH')) {
            define('STUBS_PATH', dirname(__DIR__).'/stubs');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'generators');
    }
}
