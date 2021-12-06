<?php

namespace Morrislaptop\LaravelSeedList;

use Illuminate\Database\Seeder;
use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Iterator\ClassIterator;

class LaravelSeedLister extends Seeder
{
    public function run()
    {
        $seeders = collect($this->getSeeders())
            ->map(fn ($c) => $c->getShortName())
            ->filter(fn ($c) => $c !== 'DatabaseSeeder')
            ->values()
            ->toArray();

        $chosen = $this->command->choice(
            question: 'Which seeders would you like to run?',
            choices: $seeders,
            default: null,
            attempts: null,
            multiple: true,
        );

        foreach ($chosen as $seeder) {
            $this->call('Database\\Seeders\\'.$seeder);
        }
    }

    protected function getSeeders()
    {
        $finder = new Finder;
        $classes = new ClassIterator($finder->in(database_path('seeders')));

        return $classes;
    }
}
