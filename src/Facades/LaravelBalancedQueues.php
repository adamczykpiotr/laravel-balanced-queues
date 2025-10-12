<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AdamczykPiotr\LaravelBalancedQueues\LaravelBalancedQueues
 */
class LaravelBalancedQueues extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \AdamczykPiotr\LaravelBalancedQueues\LaravelBalancedQueues::class;
    }
}
