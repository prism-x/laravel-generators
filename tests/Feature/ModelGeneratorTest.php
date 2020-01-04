<?php

namespace Tests\Feature\Generators;

use PrismX\Generators\Blueprint;
use Illuminate\Support\Facades\File;
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
        $blueprint = Blueprint::make($this->fixturePath($definition));

        $generator = new ModelGenerator(collect($blueprint)->first());

        File::shouldReceive('isDirectory')->with('app');
        File::shouldReceive('makeDirectory')->with('app');
        File::shouldReceive('exists')->with($path);

        File::shouldReceive('get')
            ->with(STUBS_PATH.'/model/class.stub')
            ->andReturn(file_get_contents(STUBS_PATH.'/model/class.stub'));

        File::shouldReceive('get')
            ->with(STUBS_PATH.'/model/fillable.stub')
            ->andReturn(file_get_contents(STUBS_PATH.'/model/fillable.stub'));

        File::shouldReceive('get')
            ->with(STUBS_PATH.'/model/casts.stub')
            ->andReturn(file_get_contents(STUBS_PATH.'/model/casts.stub'));

        File::shouldReceive('get')
            ->with(STUBS_PATH.'/model/dates.stub')
            ->andReturn(file_get_contents(STUBS_PATH.'/model/dates.stub'));

        File::shouldReceive('get')
            ->with(STUBS_PATH.'/model/method.stub')
            ->andReturn(file_get_contents(STUBS_PATH.'/model/method.stub'));

        File::shouldReceive('put')
            ->with($path, $this->fixture($model));

        File::shouldReceive('get')
            ->with($path)
            ->andReturn($this->fixture($model));

        $this->assertEquals($generator->populateStub(), $this->fixture($model));

        $generator->run();
    }

    public function modelDataProvider()
    {
        return [
            ['definitions/soft-deletes.bp', 'app/Comment.php', 'models/soft-deletes.php'],
            ['definitions/relationships.bp', 'app/Comment.php', 'models/relationships.php'],
        ];
    }
}
