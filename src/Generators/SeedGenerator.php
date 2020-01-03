<?php


namespace PrismX\Generators\Generators;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PrismX\Generators\Support\AbstractGenerator;
use PrismX\Generators\Support\Model;

class SeedGenerator extends AbstractGenerator
{
    public function output(): array
    {
        $output = [];

        $stub = File::get(STUBS_PATH . '/seed.stub');

        foreach ($this->tree as $model) {
            $path = $this->getPath($model);
            File::put($path, $this->populateStub($stub, $model));

            $output['created'][] = $path;
        }

        return $output;
    }

    protected function getPath(Model $model)
    {
        return 'database/seeds/' . $model->pluralName() . 'TableSeeder.php';
    }

    protected function populateStub(string $stub, Model $model)
    {
        $stub = str_replace('{{Namespace}}', config('generators.model_namespace'), $stub);
        $stub = str_replace('{{ClassName}}', $this->getClassName($model), $stub);
        $stub = str_replace('{{factoryClass}}', config('generators.model_namespace'). "\\{$model->name()}", $stub);

        return $stub;
    }

    protected function getClassName(Model $model)
    {
        return $model->pluralName() . 'TableSeeder';
    }
}
