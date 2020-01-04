<?php

namespace PrismX\Generators\Commands;

use Illuminate\Console\Command;
use PrismX\Generators\Blueprint;
use PrismX\Generators\Support\CSFixer;
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

        $this->blueprint = Blueprint::make($file);

        collect($this->blueprint)->each(function ($model) {
            $this->info('+--------------------');
            $this->info("| {$model->name()}");
            $this->info('+--------------------');

            $this->info((new FactoryGenerator($model))->run());
            $this->info((new MigrationGenerator($model))->run());
            $this->info((new ModelGenerator($model))->run());
            $this->info((new SeedGenerator($model))->run());

            if (config('generators.nova_resources')) {
                $this->info((new NovaResourceGenerator($model))->run());
            }

            $this->line('');
        });

        $this->info('Running CS Fixer...');
        new CSFixer();
        $this->info('Done.');
    }
}
