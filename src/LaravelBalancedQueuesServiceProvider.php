<?php

namespace AdamczykPiotr\LaravelBalancedQueues;

use AdamczykPiotr\LaravelBalancedQueues\Commands\RunBalancedQueuesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelBalancedQueuesServiceProvider extends PackageServiceProvider
{
    /**
     * @param Package $package
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-balanced-queues')
            ->hasConfigFile()
            ->hasCommand(RunBalancedQueuesCommand::class);
    }
}
