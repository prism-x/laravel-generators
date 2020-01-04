<?php

namespace PrismX\Generators\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CSFixer
{
    public function __construct()
    {
        if (File::exists(base_path('vendor/bin/php-cs-fixer')) && File::exists(base_path('.php_cs'))) {
            $this->paths()->each(function ($path) {
                $process = new Process([
                    base_path('vendor/bin/php-cs-fixer'),
                    'fix',
                    '--config=' . base_path('.php_cs'),
                    $path,
                ]);
                $process->run();

                if (! $process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
            });
        }
    }

    public function paths(): Collection
    {
        $modelsPath = Str::camel(str_replace('\\', '/', config('generators.model_namespace')));
        return collect([
            Storage::path('generators/cache'),
            base_path('database/factories'),
            base_path('database/migrations'),
            base_path('database/seeds'),
            base_path($modelsPath),
            File::isDirectory('app/Nova') ? base_path('app/nova') : null
       ])->filter();
    }
}
