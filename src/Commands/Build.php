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

        $contents = Yaml::parse(File::get($file));

        $this->blueprint = (new Lexer())->analyze($contents);

        new ModelGenerator($this->blueprint);
        new MigrationGenerator($this->blueprint);
        new FactoryGenerator($this->blueprint);
        new SeedGenerator($this->blueprint);
        new NovaResourceGenerator($this->blueprint);
    }
}
