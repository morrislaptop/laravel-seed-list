<?php

use Illuminate\Foundation\Application;
use Morrislaptop\LaravelSeedList\Tests\TestClasses\AnotherSeeder;
use Morrislaptop\LaravelSeedList\Tests\TestClasses\DatabaseSeeder;
use Morrislaptop\LaravelSeedList\Tests\TestClasses\Of;
use Morrislaptop\LaravelSeedList\Tests\TestClasses\SubSeeder;

it('lists validates and runs seeders before L9', function () {
    $this->artisan('db:seed', ['class' => DatabaseSeeder::class])
        ->expectsQuestion('Which seeder would you like to run?', ['AnotherSeeder', 'Of'])
        ->expectsOutput('Seeding: ' . AnotherSeeder::class)
        ->expectsOutput('Seeding: ' . SubSeeder::class)
        ->expectsOutput('Seeding: ' . Of::class)
        ->assertSuccessful();
    ;
})->skip(version_compare(Application::VERSION, '9.0.0', '>='), 'Only runs before L9');

it('lists validates and runs seeders after L9', function () {
    $this->artisan('db:seed', ['class' => DatabaseSeeder::class])
        ->expectsQuestion('Which seeder would you like to run?', ['AnotherSeeder', 'Of'])
        ->expectsOutputToContain(AnotherSeeder::class)
        ->expectsOutputToContain(SubSeeder::class)
        ->expectsOutputToContain(Of::class)
        ->assertSuccessful();
    ;
})->skip(version_compare(Application::VERSION, '9.0.0', '<'), 'Only runs after L9');
