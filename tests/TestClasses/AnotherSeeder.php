<?php

namespace Morrislaptop\LaravelSeedList\Tests\TestClasses;

use Illuminate\Database\Seeder;

class AnotherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SubSeeder::class);
    }
}
