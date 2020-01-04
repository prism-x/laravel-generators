<?php

namespace PrismX\Generators\Tests\Feature;

use PrismX\Generators\Blueprint;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Tests\TestCase;
use PrismX\Generators\Generators\NovaResourceGenerator;

class NovaResourceGeneratorTest extends TestCase
{
    /**
     * @test
     * @dataProvider blueprintDataProvider
     *
     * @param $definition
     * @param $path
     * @param $resource
     */
    public function it_can_create_a_seed($definition, $path, $resource)
    {
        $blueprint = Blueprint::make($this->fixturePath($definition));

        $generator = new NovaResourceGenerator(collect($blueprint)->first());

        File::shouldReceive('get')
            ->with(STUBS_PATH.'/novaResource.stub')
            ->andReturn(file_get_contents(STUBS_PATH.'/novaResource.stub'));

        File::shouldReceive('put')
            ->with($path, $this->fixture($resource));

        File::shouldReceive('get')
            ->with($path)
            ->andReturn($this->fixture($resource));

        File::shouldReceive('exists')
            ->with($path)
            ->andReturn(true);

        $this->assertEquals($generator->populateStub(), $this->fixture($resource));

        $generator->run();
    }

    public function blueprintDataProvider()
    {
        return [
            ['definitions/post.bp', 'app/Nova/Post.php', 'nova-resources/post.php'],
            ['definitions/team.bp', 'app/Nova/Team.php', 'nova-resources/team.php'],
        ];
    }
}
