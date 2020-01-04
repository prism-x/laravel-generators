<?php

namespace PrismX\Generators\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Support\AbstractGenerator;
use PrismX\Generators\Support\Model;

class MigrationGenerator extends AbstractGenerator
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->stub = File::get(STUBS_PATH . '/migration.stub');
    }

    public function populateStub(): string
    {
        $stub = $this->stub;
        $stub = str_replace('{{ClassName}}', $this->getClassName(), $stub);
        $stub = str_replace('{{TableName}}', $this->model->tableName(), $stub);
        $stub = str_replace('{{schema}}', $this->buildDefinition(), $stub);

        return $stub;
    }

    protected function buildDefinition()
    {
        $definition = '';

        foreach ($this->model->columns() as $column) {
            $dataType = $column->dataType();
            if ($column->name() === 'id') {
                $dataType = 'increments';
            } elseif ($column->dataType() === 'id') {
                $dataType = 'unsignedBigInteger';
            }

            $definition .= self::INDENT . '$table->' . $dataType . "('{$column->name()}'";

            if (! empty($column->attributes()) && $column->dataType() !== 'id') {
                $definition .= ', ';
                if (in_array($column->dataType(), ['set', 'enum'])) {
                    $definition .= json_encode($column->attributes());
                } else {
                    $definition .= implode(', ', $column->attributes());
                }
            }
            $definition .= ')';

            foreach ($column->modifiers() as $modifier) {
                if (is_array($modifier)) {
                    $definition .= "->" . key($modifier) . "(" . current($modifier) . ")";
                } else {
                    $definition .= '->' . $modifier . '()';
                }
            }

            $definition .= ';' . PHP_EOL;
        }

        if ($this->model->usesSoftDeletes()) {
            $definition .= self::INDENT . '$table->' . $this->model->softDeletesDataType() . '();' . PHP_EOL;
        }

        if ($this->model->usesTimestamps()) {
            $definition .= self::INDENT . '$table->' . $this->model->timestampsDataType() . '();' . PHP_EOL;
        }

        return trim($definition);
    }

    protected function getClassName()
    {
        return 'Create' . Str::studly($this->model->tableName()) . 'Table';
    }

    protected function getPath(): string
    {
        $check = glob('database/migrations/*_create_' . $this->model->tableName() . '_table.php');

        return $check[0] ?? 'database/migrations/' . \Carbon\Carbon::now()->format('Y_m_d_His') . '_create_' . $this->model->tableName() . '_table.php';
    }
}
