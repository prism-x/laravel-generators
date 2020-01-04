<?php

namespace Tests\Feature\Generators;

use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Support\Lexer;
use PrismX\Generators\Tests\TestCase;
use PrismX\Generators\Generators\ModelGenerator;

class ModelGeneratorTest extends TestCase
{
    /**
     * @test
     * @dataProvider modelDataProvider
     *
     * @param $definition
     * @param $path
     * @param $model
     */
    public function it_can_create_a_model($definition, $path, $model)
    {
        $contents = Yaml::parse($this->parseDefinition($this->fixture($definition)));
        $blueprint = collect((new Lexer())->analyze($contents))->first();

        $generator = new ModelGenerator($blueprint);

        File::shouldReceive('isDirectory')->with('app/Models');
        File::shouldReceive('makeDirectory')->with('app/Models');
        File::shouldReceive('exists')->with($path);

        File::shouldReceive('get')
            ->with(STUBS_PATH . '/model/class.stub')
            ->andReturn(file_get_contents(STUBS_PATH . '/model/class.stub'));

        File::shouldReceive('get')
            ->with(STUBS_PATH . '/model/fillable.stub')
            ->andReturn(file_get_contents(STUBS_PATH . '/model/fillable.stub'));

        File::shouldReceive('get')
            ->with(STUBS_PATH . '/model/casts.stub')
            ->andReturn(file_get_contents(STUBS_PATH . '/model/casts.stub'));

        File::shouldReceive('get')
            ->with(STUBS_PATH . '/model/dates.stub')
            ->andReturn(file_get_contents(STUBS_PATH . '/model/dates.stub'));

        File::shouldReceive('get')
            ->with(STUBS_PATH . '/model/method.stub')
            ->andReturn(file_get_contents(STUBS_PATH . '/model/method.stub'));

        File::shouldReceive('put')
            ->with($path, $this->fixture($model));

        $stub = $generator->populateStub(file_get_contents(STUBS_PATH . '/model/class.stub'));

        $this->assertEquals($stub, $this->fixture($model));

        $generator->run();

    }

    public function modelDataProvider()
    {
        return [
            ['definitions/soft-deletes.bp', 'app/Models/Comment.php', 'models/soft-deletes.php'],
            ['definitions/relationships.bp', 'app/Models/Comment.php', 'models/relationships.php'],
        ];
    }
}
