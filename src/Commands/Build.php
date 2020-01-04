<?php

namespace PrismX\Generators\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Support\Lexer;
use PrismX\Generators\Generators\SeedGenerator;
use PrismX\Generators\Generators\ModelGenerator;
use PrismX\Generators\Generators\FactoryGenerator;
use PrismX\Generators\Generators\MigrationGenerator;
use PrismX\Generators\Generators\NovaResourceGenerator;

class Build extends Command
{
    protected $signature = 'generator:build {blueprint=blueprint.yml}';

    protected $description = 'Command description';
    protected $blueprint = [];

    public function handle()
    {
        $file = $this->argument('blueprint');
        if (! file_exists($file)) {
            $this->error('Blueprint file could not be found: ' . $file);
        }

        $content = preg_replace_callback('/^(\s+)(id|timestamps(Tz)?|softDeletes(Tz)?)$/mi', function ($matches) {
            return $matches[1] . strtolower($matches[2]) . ': ' . $matches[2];
        }, File::get($file));

        $contents = Yaml::parse($content);

        $this->blueprint = (new Lexer())->analyze($contents);

        collect($this->blueprint)->each(function ($model) {
            $this->info('********************');
            $this->info("* {$model->name()}");
            $this->info('********************');
            $this->info((new FactoryGenerator($model))->run());
            $this->info((new MigrationGenerator($model))->run());
            $this->info((new ModelGenerator($model))->run());
            $this->info((new NovaResourceGenerator($model))->run());
            $this->info((new SeedGenerator($model))->run());
            $this->line('');
        });
    }
}
