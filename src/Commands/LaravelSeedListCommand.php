<?php

namespace Morrislaptop\LaravelSeedList\Commands;

use Illuminate\Console\Command;

class LaravelSeedListCommand extends Command
{
    public $signature = 'laravel-seed-list';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
