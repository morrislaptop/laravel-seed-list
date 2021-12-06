<?php

namespace Morrislaptop\LaravelSeedList;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Morrislaptop\LaravelSeedList\Commands\LaravelSeedListCommand;

class LaravelSeedListServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-seed-list')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-seed-list_table')
            ->hasCommand(LaravelSeedListCommand::class);
    }
}
