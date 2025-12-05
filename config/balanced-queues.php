<?php

use AdamczykPiotr\LaravelBalancedQueues\Enums\JobWorkloadType;
use AdamczykPiotr\LaravelBalancedQueues\Services\Cpu\CpuCoreConfigurationResolver;
use AdamczykPiotr\LaravelBalancedQueues\Services\Cpu\CpuCoreCountProvider;

$CPU_CORES = CpuCoreConfigurationResolver::CPU_CORES;

return [

    /*
    |--------------------------------------------------------------------------
    | Package configuration
    |--------------------------------------------------------------------------
    */

    'package' => [
        'cpu_core_count' => CpuCoreCountProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue names handled by Supervisor
    |--------------------------------------------------------------------------
    */

    'queues' => [
        JobWorkloadType::DEFAULT->value => 1,

        JobWorkloadType::CPU_HIGH->value => $CPU_CORES / 2,
        JobWorkloadType::CPU_MEDIUM->value => $CPU_CORES,

        JobWorkloadType::NETWORK_HIGH_BANDWIDTH->value => 2,
        JobWorkloadType::NETWORK_HIGH_REQUESTS->value => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional laravel `artisan queue:work` configuration
    |--------------------------------------------------------------------------
    */

    'worker_options' => [
        // '--silent'
        // '--timeout' => 60
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Supervisor configuration header
    |--------------------------------------------------------------------------
    */

    'supervisor' => [
        'header' => [],

        /*
        |--------------------------------------------------------------------------
        | Signal handling for graceful shutdowns
        |--------------------------------------------------------------------------
        |
        | These options improve handling of graceful shutdowns, especially in
        | dockerized/orchestrated environments. Configure stopwaitsecs to be
        | greater than the longest job duration (worker timeout value or higher).
        |
        */

        'stopsignal' => 'SIGTERM',
        'stopwaitsecs' => 605,
        'stopasgroup' => true,
        'killasgroup' => true,
    ],
];
