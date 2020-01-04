<?php

namespace PrismX\Generators\Generators;

use Illuminate\Support\Facades\File;
use PrismX\Generators\Support\AbstractGenerator;

class SeedGenerator extends AbstractGenerator
{
    public function run()
    {
        $stub = File::get(STUBS_PATH . '/seed.stub');
        File::put($this->getPath(), $this->populateStub($stub));
        return "{$this->model->name()} seed created successfully <comment>[{$this->getPath()}]</comment>";
    }

    protected function getPath()
    {
        return 'database/seeds/' . $this->model->pluralName() . 'TableSeeder.php';
    }

    protected function populateStub(string $stub)
    {
        $stub = str_replace('{{Namespace}}', config('generators.model_namespace'), $stub);
        $stub = str_replace('{{ClassName}}', $this->getClassName(), $stub);
        $stub = str_replace('{{factoryClass}}', config('generators.model_namespace'). "\\{$this->model->name()}", $stub);

        return $stub;
    }

    protected function getClassName()
    {
        return $this->model->pluralName() . 'TableSeeder';
    }
}
