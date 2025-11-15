<?php

use AdamczykPiotr\LaravelBalancedQueues\Enums\JobWorkloadType;
use AdamczykPiotr\LaravelBalancedQueues\Services\Cpu\CpuCoreConfigurationResolver;

$CPU_CORES = CpuCoreConfigurationResolver::CPU_CORES;

return [

    /*
    |--------------------------------------------------------------------------
    | Package configuration
    |--------------------------------------------------------------------------
    */

    'package' => [
        'cpu_core_count' => 4, // CpuCoreCountProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue names handled by Supervisor
    |--------------------------------------------------------------------------
    */

    'queues' => [
        JobWorkloadType::DEFAULT->value => 1,

        JobWorkloadType::CPU_HIGH->value => $CPU_CORES,
        JobWorkloadType::CPU_MEDIUM->value => 4 * $CPU_CORES,

        JobWorkloadType::NETWORK_HIGH_BANDWIDTH->value => 5,
        JobWorkloadType::NETWORK_HIGH_REQUESTS->value => 50,
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
    ],
];
