<?php

namespace Morrislaptop\LaravelSeedList\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Morrislaptop\LaravelSeedList\LaravelSeedList
 */
class LaravelSeedList extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-seed-list';
    }
}
