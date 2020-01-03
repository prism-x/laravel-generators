<?php


namespace PrismX\Generators\Generators;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PrismX\Generators\Support\AbstractGenerator;
use PrismX\Generators\Support\Model;

class FactoryGenerator extends AbstractGenerator
{
    const INDENT = '        ';

    public function output(): array
    {
        $output = [];

        $stub = File::get(STUBS_PATH . '/factory.stub');

        foreach ($this->tree as $model) {
            $path = $this->getPath($model);
            File::put($path, $this->populateStub($stub, $model));

            $output['created'][] = $path;
        }

        return $output;
    }

    protected function getPath(Model $model)
    {
        return 'database/factories/' . $model->name() . 'Factory.php';
    }

    protected function populateStub(string $stub, Model $model)
    {
        $stub = str_replace('{{Namespace}}', config('generators.model_namespace'), $stub);
        $stub = str_replace('{{ClassName}}', $model->name(), $stub);
        $stub = str_replace('{{fields}}', $this->buildDefinition($model), $stub);

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
