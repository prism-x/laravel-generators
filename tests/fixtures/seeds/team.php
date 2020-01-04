<?php

use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{
    public function run()
    {
        factory(App\Team::class, 20)->create();
    }
}
