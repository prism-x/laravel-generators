<?php

namespace PrismX\Generators\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CSFixer
{
    public function __construct()
    {
        if (File::exists(base_path('vendor/bin/php-cs-fixer')) && File::exists(base_path('.php_cs'))) {
            $process = new Process(array_merge([
                base_path('vendor/bin/php-cs-fixer'),
                'fix',
                '--config='.base_path('.php_cs'),
            ], $this->paths()->toArray()));

            $process->run();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            echo $process->getOutput();
        }
    }

    public function paths(): Collection
    {
        $modelsPath = Str::camel(str_replace('\\', '/', config('generators.model_namespace')));

        $models = glob(base_path($modelsPath.'/*.php'));

        return collect([
            Storage::path('generators/cache'),
            base_path('database/factories'),
            base_path('database/migrations'),
            base_path('database/seeds'),
            File::isDirectory('app/Nova') ? base_path('app/nova') : null,
        ])->concat($models)->filter();
    }
}
