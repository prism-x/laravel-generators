<?php

namespace PrismX\Generators\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Support\Model;
use PrismX\Generators\Support\Column;
use PrismX\Generators\Support\AbstractGenerator;

class ModelGenerator extends AbstractGenerator
{
    protected $dir;

    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->stub = File::get(STUBS_PATH . '/model/class.stub');

        $this->dir = Str::camel(str_replace('\\', '/', config('generators.model_namespace')));

        if (! File::isDirectory($this->dir)) {
            File::makeDirectory($this->dir);
        }
    }

    public function populateStub(): string
    {
        $stub = $this->stub;
        $stub = str_replace('{{Namespace}}', config('generators.model_namespace'), $stub);
        $stub = str_replace('{{ClassName}}', $this->model->name(), $stub);
        $body = $this->buildProperties();
        $body .= PHP_EOL;
        $body .= $this->buildRelationships();
        $stub = str_replace('{{body}}', trim($body), $stub);
        $stub = $this->addTraits($stub);

        return $stub;
    }

    public function getPath(): string
    {
        return "{$this->dir}/{$this->model->name()}.php";
    }

    private function buildProperties()
    {
        $properties = '';
        $columns = $this->fillableColumns($this->model->columns());
        if (! empty($columns)) {
            $properties .= PHP_EOL . str_replace('[]', $this->pretty_print_array($columns, false), $this->getStub('fillable'));
        } else {
            $properties .= $this->getStub('fillable');
        }
        $columns = $this->castableColumns($this->model->columns());
        if (! empty($columns)) {
            $properties .= PHP_EOL . str_replace('[]', $this->pretty_print_array($columns), $this->getStub('casts'));
        }
        $columns = $this->dateColumns($this->model->columns());
        if (! empty($columns)) {
            $properties .= PHP_EOL . str_replace('[]', $this->pretty_print_array($columns, false), $this->getStub('dates'));
        }

        return trim($properties);
    }

    private function buildRelationships()
    {
        $columns = array_filter($this->model->columns(), function (Column $column) {
            return Str::endsWith($column->name(), '_id');
        });
        if (empty($columns)) {
            return '';
        }
        $methods = '';
        $template = $this->getStub('method');

        foreach ($columns as $column) {
            $name = Str::substr($column->name(), 0, -3);
            $class = Str::studly($column->attributes()[0] ?? $name);
            $relationship = sprintf("\$this->belongsTo(\\" . config('generators.model_namespace') . "\%s::class)", $class);
            $method = str_replace('{{MethodName}}', Str::camel($name), $template);
            $method = str_replace('null', $relationship, $method);
            $methods .= PHP_EOL . $method;
        }

        return $methods;
    }

    private function fillableColumns(array $columns)
    {
        return array_diff(array_keys($columns), [
            'id',
            'password',
            'deleted_at',
            'created_at',
            'updated_at',
        ]);
    }

    private function castableColumns(array $columns)
    {
        return array_filter(array_map(
            function (Column $column) {
                return $this->castForColumn($column);
            },
            $columns
        ));
    }

    private function dateColumns(array $columns)
    {
        return array_map(
            function (Column $column) {
                return $column->name();
            },
            array_filter($columns, function (Column $column) {
                return stripos($column->dataType(), 'datetime') !== false
                    || stripos($column->dataType(), 'timestamp') !== false;
            })
        );
    }

    private function castForColumn(Column $column)
    {
        if (stripos($column->dataType(), 'integer')) {
            return 'integer';
        }
        if (in_array($column->dataType(), ['boolean', 'double', 'float'])) {
            return strtolower($column->dataType());
        }
        if (in_array($column->dataType(), ['decimal', 'unsignedDecimal'])) {
            if ($column->attributes()) {
                return 'decimal:' . $column->attributes()[1];
            }

            return 'decimal';
        }

        return null;
    }

    private function pretty_print_array(array $data, $assoc = true)
    {
        $output = var_export($data, true);
        $output = preg_replace('/^\s+/m', '        ', $output);
        $output = preg_replace(['/^array\s\(/', "/\)$/"], ['[', '    ]'], $output);
        if (! $assoc) {
            $output = preg_replace('/^(\s+)[^=]+=>\s+/m', '$1', $output);
        }

        return trim($output);
    }

    private function getStub(string $stub)
    {
        static $stubs = [];
        if (empty($stubs[$stub])) {
            $stubs[$stub] = File::get(STUBS_PATH . '/model/' . $stub . '.stub');
        }

        return $stubs[$stub];
    }

    private function addTraits($stub)
    {
        if (! $this->model->usesSoftDeletes()) {
            return $stub;
        }
        $stub = str_replace('use Illuminate\\Database\\Eloquent\\Model;', 'use Illuminate\\Database\\Eloquent\\Model;' . PHP_EOL . 'use Illuminate\\Database\\Eloquent\\SoftDeletes;', $stub);
        $stub = preg_replace('/^\\{$/m', '{' . PHP_EOL . '    use SoftDeletes;' . PHP_EOL, $stub);

        return $stub;
    }
}
