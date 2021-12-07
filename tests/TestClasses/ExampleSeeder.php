<?php

namespace Morrislaptop\LaravelSeedList\Tests\TestClasses;

use Illuminate\Database\Seeder;

class ExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AnotherSeeder::class);

        $this->call([
            Series::class,
            Of::class,
            Seeders::class,
        ]);
    }
}
