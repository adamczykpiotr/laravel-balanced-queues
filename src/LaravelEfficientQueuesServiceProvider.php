<?php

namespace AdamczykPiotr\LaravelEfficientQueues;

use AdamczykPiotr\LaravelEfficientQueues\Commands\LaravelEfficientQueuesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelEfficientQueuesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-efficient-queues')
            ->hasConfigFile()
            ->hasCommand(LaravelEfficientQueuesCommand::class);
    }
}
