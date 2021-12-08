<?php

namespace Morrislaptop\LaravelSeedList\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Morrislaptop\LaravelSeedList\LaravelSeedListServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Morrislaptop\\LaravelSeedList\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelSeedListServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('seed-list.seeders_path', __DIR__.'/TestClasses');
        config()->set('seed-list.seeders_namespace', 'Morrislaptop\\LaravelSeedList\\Tests\\TestClasses\\');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-seed-list_table.php.stub';
        $migration->up();
        */
    }
}
