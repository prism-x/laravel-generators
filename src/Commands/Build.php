<?php

namespace PrismX\Generators\Commands;

use Illuminate\Console\Command;
use PrismX\Generators\Blueprint;
use PrismX\Generators\Support\CSFixer;
use Symfony\Component\Console\Helper\Table;
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
            $this->error('Blueprint file could not be found: '.$file);
        }

        $this->blueprint = Blueprint::make($file);

        collect($this->blueprint)->mapWithKeys(function ($model) {
            return [
                $model->name() => collect([
                    (new FactoryGenerator($model))->run(),
                    (new MigrationGenerator($model))->run(),
                    (new ModelGenerator($model))->run(),
                    (new SeedGenerator($model))->run(),
                    config('generators.nova_resources') ? (new NovaResourceGenerator($model))->run() : null,
                ])->filter()->values(),
            ];
        })->filter(function ($model) {
            return ! $model->isEmpty();
        })->each(function ($values, $model) {
            $table = new Table($this->output);
            $table->setHeaders([$model]);

            $table->setRows($values->map(function ($value) {
                return [$value];
            })->toArray());

            // Render the table to the output.
            $table->render();
        });

//        $this->info('Running CS Fixer...');
//        new CSFixer();
        $this->info('Done.');
    }
}
