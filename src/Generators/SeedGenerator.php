<?php

namespace PrismX\Generators\Generators;

use Illuminate\Support\Facades\File;
use PrismX\Generators\Support\AbstractGenerator;
use PrismX\Generators\Support\Model;

class SeedGenerator extends AbstractGenerator
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->stub = File::get(STUBS_PATH . '/seed.stub');
    }

    protected function getPath():string
    {
        return 'database/seeds/' . $this->model->pluralName() . 'TableSeeder.php';
    }

    public function populateStub():string
    {
        $stub = $this->stub;
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
