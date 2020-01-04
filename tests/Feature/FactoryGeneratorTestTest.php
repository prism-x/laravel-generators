<?php

namespace PrismX\Generators\Tests\Feature;

use PrismX\Generators\Blueprint;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Tests\TestCase;
use PrismX\Generators\Generators\FactoryGenerator;

class FactoryGeneratorTestTest extends TestCase
{
    /**
     * @test
     * @dataProvider blueprintDataProvider
     *
     * @param $definition
     * @param $path
     * @param $factory
     */
    public function it_can_create_a_factory($definition, $path, $factory)
    {
        $blueprint = Blueprint::make($this->fixturePath($definition));

        $generator = new FactoryGenerator(collect($blueprint)->first());

        File::shouldReceive('get')
            ->with(STUBS_PATH . '/factory.stub')
            ->andReturn(file_get_contents(STUBS_PATH . '/factory.stub'));

        File::shouldReceive('put')
            ->with($path, $this->fixture($factory));

        File::shouldReceive('get')
            ->with($path)
            ->andReturn($this->fixture($factory));

        File::shouldReceive('exists')
            ->with($path)
            ->andReturn(true);

        $this->assertEquals($generator->populateStub(), $this->fixture($factory));

        $generator->run();
    }

    public function blueprintDataProvider()
    {
        return [
            ['definitions/post.bp', 'database/factories/PostFactory.php', 'factories/post.php'],
            ['definitions/team.bp', 'database/factories/TeamFactory.php', 'factories/team.php']
        ];
    }
}
