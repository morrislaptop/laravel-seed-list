<?php

use Morrislaptop\LaravelSeedList\Tests\TestClasses\AnotherSeeder;
use Morrislaptop\LaravelSeedList\Tests\TestClasses\DatabaseSeeder;
use Morrislaptop\LaravelSeedList\Tests\TestClasses\Of;
use Morrislaptop\LaravelSeedList\Tests\TestClasses\SubSeeder;

it('lists validates and runs seeders', function () {
    $this->artisan('db:seed', ['class' => DatabaseSeeder::class])
        ->expectsQuestion('Which seeder would you like to run?', ['AnotherSeeder', 'Of'])
        ->expectsOutput('Seeding: ' . AnotherSeeder::class)
        ->expectsOutput('Seeding: ' . SubSeeder::class)
        // ->expectsOutput('Seeded: ' . AnotherSeeder::class . ' (0.00ms)')
        // ->expectsOutput('Seeded: ' . SubSeeder::class . ' (0.00ms)')
        ->expectsOutput('Seeding: ' . Of::class)
        // ->expectsOutput('Seeded: ' . Of::class . ' (0.00ms)')
        ->expectsOutput('Database seeding completed successfully.')
    ;
});
