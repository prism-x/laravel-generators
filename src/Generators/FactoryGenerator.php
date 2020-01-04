<?php

namespace PrismX\Generators\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Support\AbstractGenerator;

class FactoryGenerator extends AbstractGenerator
{
    const INDENT = '        ';

    public function run(): string
    {
        $stub = File::get(STUBS_PATH . '/factory.stub');
        File::put($this->getPath(), $this->populateStub($stub));
        return "{$this->model->name()} factory created successfully <comment>[{$this->getPath()}]</comment>";
    }

    public function getPath()
    {
        return 'database/factories/' . $this->model->name() . 'Factory.php';
    }

    public function populateStub(string $stub)
    {
        $stub = str_replace('{{Namespace}}', config('generators.model_namespace'), $stub);
        $stub = str_replace('{{ClassName}}', $this->model->name(), $stub);
        $stub = str_replace('{{fields}}', $this->buildDefinition(), $stub);

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

                $definition .= self::INDENT . "'{$column->name()}' => ";
                $definition .= sprintf("factory(\\".config('generators.model_namespace')."\%s::class)", $class);
                $definition .= ',' . PHP_EOL;
            } else {
                $definition .= self::INDENT . "'{$column->name()}' => ";
                $faker = $this->fakerData($column->name()) ?? $this->fakerDataType($column->dataType());
                $definition .= '$faker->' . $faker;
                $definition .= ',' . PHP_EOL;
            }
        }

        return trim($definition);
    }

    protected function fakerData(string $name)
    {
        static $fakeableNames = [
            'city' => 'city',
            'company' => 'company',
            'content' => 'paragraphs(3, true)',
            'country' => 'country',
            'description' => 'text',
            'email' => 'safeEmail',
            'first_name' => 'firstName',
            'firstname' => 'firstName',
            'guid' => 'uuid',
            'last_name' => 'lastName',
            'lastname' => 'lastName',
            'lat' => 'latitude',
            'latitude' => 'latitude',
            'lng' => 'longitude',
            'longitude' => 'longitude',
            'name' => 'name',
            'password' => 'password',
            'phone' => 'phoneNumber',
            'phone_number' => 'phoneNumber',
            'postcode' => 'postcode',
            'postal_code' => 'postcode',
            'slug' => 'slug',
            'street' => 'streetName',
            'address1' => 'streetAddress',
            'address2' => 'secondaryAddress',
            'summary' => 'text',
            'title' => 'sentence(4)',
            'url' => 'url',
            'user_name' => 'userName',
            'username' => 'userName',
            'uuid' => 'uuid',
            'zip' => 'postcode',
        ];

        return $fakeableNames[$name] ?? null;
    }

    protected function fakerDataType(string $type)
    {
        $fakeableTypes = [
            'id' => 'randomDigitNotNull',
            'string' => 'word',
            'text' => 'text',
            'date' => 'date()',
            'time' => 'time()',
            'guid' => 'word',
            'datetimetz' => 'dateTime()',
            'datetime' => 'dateTime()',
            'timestamp' => 'dateTime()',
            'integer' => 'randomNumber()',
            'bigint' => 'randomNumber()',
            'smallint' => 'randomNumber()',
            'decimal' => 'randomFloat()',
            'float' => 'randomFloat()',
            'boolean' => 'boolean'
        ];

        return $fakeableTypes[$type] ?? null;
    }
}
