<?php

namespace PrismX\Generators\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PrismX\Generators\Support\AbstractGenerator;
use PrismX\Generators\Support\Model;

class NovaResourceGenerator extends AbstractGenerator
{
    protected $imports = [
        'Laravel\Nova\Fields\ID',
        'Illuminate\Http\Request',
    ];

    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->stub = File::get(STUBS_PATH.'/novaResource.stub');
    }

    protected function getPath(): string
    {
        return "app/Nova/{$this->model->name()}.php";
    }

    public function populateStub(): string
    {
        $stub = $this->stub;
        $stub = str_replace('{{ClassName}}', $this->model->name(), $stub);
        $stub = str_replace('{{ModelName}}', '\\'.config('generators.model_namespace')."\\{$this->model->name()}", $stub);
        $stub = str_replace('{{fields}}', $this->buildDefinition(), $stub);
        $stub = str_replace('{{imports}}', $this->buildImports(), $stub);

        return $stub;
    }

    protected function buildDefinition()
    {
        $definition = '';

        foreach ($this->model->columns() as $column) {
            if ($column->name() === 'id') {
                continue;
            }

            if ($column->dataType() === 'id') {
                $name = Str::substr($column->name(), 0, -3);
                $class = Str::studly($column->attributes()[0] ?? $name);

                $definition .= self::INDENT;
                $definition .= sprintf("BelongsTo::make('%s')", $class);
                $definition .= ','.PHP_EOL;

                $this->imports[] = 'Laravel\Nova\Fields\BelongsTo';
            } else {
                $fieldType = $this->novaData($column->name()) ?? $this->novaDataType($column->dataType());

                $definition .= self::INDENT;
                $definition .= sprintf("%s::make('%s')", $fieldType, $this->titleCase($column->name()));
                $definition .= ','.PHP_EOL;

                $this->imports[] = "Laravel\Nova\Fields\\{$fieldType}";
            }
        }

        return trim($definition);
    }

    protected function titleCase($string)
    {
        return Str::title(str_replace('_', ' ', $string));
    }

    protected function buildImports()
    {
        return collect($this->imports)->unique()->sort(function ($a, $b) {
            return strlen($a) - strlen($b);
        })->map(function ($import) {
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
            'boolean' => 'Boolean',
        ];

        return $novaTypes[$type] ?? 'Text';
    }
}
