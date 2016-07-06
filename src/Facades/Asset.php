<?php namespace CupOfTea\AssetManager\Facades;

use Illuminate\Support\Facades\Facade;
use CupOfTea\AssetManager\Contracts\Provider;

class Asset extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Provider::class;
    }
}
