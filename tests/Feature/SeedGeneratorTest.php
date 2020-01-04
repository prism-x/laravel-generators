<?php

namespace PrismX\Generators\Tests\Feature;

use PrismX\Generators\Blueprint;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Tests\TestCase;
use PrismX\Generators\Generators\SeedGenerator;

class SeedGeneratorTest extends TestCase
{
    /**
     * @test
     * @dataProvider blueprintDataProvider
     *
     * @param $definition
     * @param $path
     * @param $seed
     */
    public function it_can_create_a_seed($definition, $path, $seed)
    {
        $blueprint = Blueprint::make($this->fixturePath($definition));

        $generator = new SeedGenerator(collect($blueprint)->first());

        File::shouldReceive('get')
            ->with(STUBS_PATH.'/seed.stub')
            ->andReturn(file_get_contents(STUBS_PATH.'/seed.stub'));

        File::shouldReceive('put')
            ->with($path, $this->fixture($seed));

        File::shouldReceive('get')
            ->with($path)
            ->andReturn($this->fixture($seed));

        File::shouldReceive('exists')
            ->with($path)
            ->andReturn(true);

        $this->assertEquals($generator->populateStub(), $this->fixture($seed));

        $generator->run();
    }

    public function blueprintDataProvider()
    {
        return [
            ['definitions/post.bp', 'database/seeds/PostsTableSeeder.php', 'seeds/post.php'],
            ['definitions/team.bp', 'database/seeds/TeamsTableSeeder.php', 'seeds/team.php'],
        ];
    }
}
