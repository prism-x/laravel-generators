<?php

namespace PrismX\Generators\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Support\Model;
use PrismX\Generators\Support\AbstractGenerator;

class NovaResourceGenerator extends AbstractGenerator
{
    protected $imports = [];
    public function output()
    {
        if (! config('generators.generate_nova_resource')) {
            return null;
        }
        $output = [];

        $stub = File::get(STUBS_PATH . '/novaResource.stub');

        foreach ($this->tree as $model) {
            $path = $this->getPath($model);
            File::put($path, $this->populateStub($stub, $model));

//            $this->info(crea)$path;
        }

        return $output;
    }

    protected function getPath(Model $model)
    {
        return "app/Nova/{$model->name()}.php";
    }

    protected function populateStub(string $stub, Model $model)
    {
        $stub = str_replace('{{ClassName}}', $model->name(), $stub);
        $stub = str_replace('{{ModelName}}', '\\'.config('generators.model_namespace')."\\{$model->name()}", $stub);
        $stub = str_replace('{{fields}}', $this->buildDefinition($model), $stub);
        $stub = str_replace('{{imports}}', $this->buildImports($model), $stub);

        return $stub;
    }

    protected function buildDefinition(Model $model)
    {
        $definition = '';

        foreach ($model->columns() as $column) {
            if ($column->name() === 'id') {
                continue;
            }

            if ($column->dataType() === 'id') {
                $name = Str::substr($column->name(), 0, -3);
                $class = Str::studly($column->attributes()[0] ?? $name);

                $definition .= self::INDENT;
                $definition .= sprintf("BelongsTo::make('%s')", $class);
                $definition .= ',' . PHP_EOL;

                $this->imports[] = 'Laravel\Nova\Fields\BelongsTo';
            } else {
                $fieldType = $this->novaData($column->name()) ?? $this->novaDataType($column->dataType());

                $definition .= self::INDENT;
                $definition .= sprintf("%s::make('%s')", $fieldType, $this->titleCase($column->name()));
                $definition .= ',' . PHP_EOL;

                $this->imports[] = "Laravel\Nova\Fields\\{$fieldType}";
            }
        }

        return trim($definition);
    }

    protected function titleCase($string)
    {
        return Str::title(str_replace('_', ' ', $string));
    }

    protected function buildImports(Model $model)
    {
        return collect($this->imports)->unique()->map(function ($import) {
            return "use {$import};";
        })->implode(PHP_EOL);
    }

    protected function novaData(string $name)
    {
        $novaNames = [
            'country' => 'Country',
            'currency' => 'Currency',
            'password' => 'Password',
            'image' => 'Image',
            'picture' => 'Image',
            'avatar' => 'Avatar',
            'file' => 'File',
            'address' => 'Place',
            'address1' => 'Place',
            'address2' => 'Place',
        ];

        return $novaNames[$name] ?? null;
    }

    protected function novaDataType(string $type)
    {
        $novaTypes = [
            'id' => 'ID',
            'string' => 'Text',
            'text' => 'Trix',
            'longText' => 'Trix',
            'date' => 'Date',
            'time' => 'Time',
            'guid' => 'Text',
            'datetimetz' => 'DateTime',
            'datetime' => 'DateTime',
            'timestamp' => 'DateTime',
            'integer' => 'Number',
            'bigint' => 'Number',
            'smallint' => 'Number',
            'decimal' => 'Number',
            'float' => 'Number',
            'boolean' => 'Boolean'
        ];

        return $novaTypes[$type] ?? 'Text';
    }
}
