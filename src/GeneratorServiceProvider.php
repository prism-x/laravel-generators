<?php

namespace PrismX\Generators;

use Illuminate\Support\ServiceProvider;
use PrismX\Generators\Commands\Build;

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
