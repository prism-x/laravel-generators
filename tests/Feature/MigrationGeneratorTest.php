<?php

namespace PrismX\Generators\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use PrismX\Generators\Blueprint;
use PrismX\Generators\Generators\MigrationGenerator;
use PrismX\Generators\Tests\TestCase;

class MigrationGeneratorTest extends TestCase
{
    /**
     * @test
     * @dataProvider blueprintDataProvider
     *
     * @param $definition
     * @param $path
     * @param $migration
     */
    public function it_can_create_a_migration($definition, $path, $migration)
    {
        $blueprint = Blueprint::make($this->fixturePath($definition));
        $generator = new MigrationGenerator(collect($blueprint)->first());

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $timestamp_path = str_replace('timestamp', $now->format('Y_m_d_His'), $path);

        File::shouldReceive('get')
            ->with(STUBS_PATH.'/migration.stub')
            ->andReturn(file_get_contents(STUBS_PATH.'/migration.stub'));

        File::shouldReceive('put')
            ->with($timestamp_path, $this->fixture($migration));

        File::shouldReceive('get')
            ->with($timestamp_path)
            ->andReturn($this->fixture($migration));

        File::shouldReceive('exists')
            ->with($timestamp_path)
            ->andReturn(true);

        $this->assertEquals($generator->populateStub(), $this->fixture($migration));

        $generator->run();
    }

    public function blueprintDataProvider()
    {
        return [
            ['definitions/model-identities.bp', 'database/migrations/timestamp_create_relationships_table.php', 'migrations/identity-columns.php'],
            ['definitions/model-modifiers.bp', 'database/migrations/timestamp_create_modifiers_table.php', 'migrations/modifiers.php'],
            ['definitions/soft-deletes.bp', 'database/migrations/timestamp_create_comments_table.php', 'migrations/soft-deletes.php'],
            ['definitions/relationships.bp', 'database/migrations/timestamp_create_comments_table.php', 'migrations/relationships.php'],
        ];
    }
}
