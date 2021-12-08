<?php

use Morrislaptop\LaravelSeedList\Tests\TestClasses\AnotherSeeder;
use Morrislaptop\LaravelSeedList\Tests\TestClasses\DatabaseSeeder;
use Morrislaptop\LaravelSeedList\Tests\TestClasses\Of;
use Morrislaptop\LaravelSeedList\Tests\TestClasses\SubSeeder;

it('lists, validates and runs seeders', function () {
    $this->artisan('db:seed', ['class' => DatabaseSeeder::class])
        ->expectsOutput(<<<EOT
1. AnotherSeeder
• SubSeeder
2. ExampleSeeder
• AnotherSeeder
• SubSeeder
• Series
• Of
• Seeders
3. Of
4. Seeders
5. Series
6. SubSeeder
EOT)
        ->expectsQuestion('Which seeder(s) would you like to run? Separate multiple choices with a comma', '0,1,3')
        ->expectsOutput('The following choices are not valid: 0')
        ->expectsQuestion('Which seeder(s) would you like to run? Separate multiple choices with a comma', '1,3')
        ->expectsOutput('Seeding: ' . AnotherSeeder::class)
        ->expectsOutput('Seeding: ' . SubSeeder::class)
        // ->expectsOutput('Seeded: ' . AnotherSeeder::class . ' (0.00ms)')
        // ->expectsOutput('Seeded: ' . SubSeeder::class . ' (0.00ms)')
        ->expectsOutput('Seeding: ' . Of::class)
        // ->expectsOutput('Seeded: ' . Of::class . ' (0.00ms)')
        ->expectsOutput('Database seeding completed successfully.')
        ;
});
