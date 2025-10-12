<?php

namespace AdamczykPiotr\LaravelEfficientQueues\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AdamczykPiotr\LaravelEfficientQueues\LaravelEfficientQueues
 */
class LaravelEfficientQueues extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AdamczykPiotr\LaravelEfficientQueues\LaravelEfficientQueues::class;
    }
}
