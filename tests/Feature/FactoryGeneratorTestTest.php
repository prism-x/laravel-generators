<?php

namespace PrismX\Generators\Tests\Feature;

use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Support\Lexer;
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
        $contents = Yaml::parse($this->parseDefinition($this->fixture($definition)));
        $blueprint = collect((new Lexer())->analyze($contents))->first();
        $generator = new FactoryGenerator($blueprint);

        File::shouldReceive('get')
            ->with(STUBS_PATH . '/factory.stub')
            ->andReturn(file_get_contents(STUBS_PATH . '/factory.stub'));

        File::shouldReceive('put')
            ->with($path, $this->fixture($factory));

        $stub = $generator->populateStub(file_get_contents(STUBS_PATH . '/factory.stub'));

        $this->assertEquals($stub, $this->fixture($factory));

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
