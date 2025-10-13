<?php

use AdamczykPiotr\LaravelBalancedQueues\Enums\JobWorkloadType;
use AdamczykPiotr\LaravelBalancedQueues\Services\Cpu\CpuCoreConfigurationResolver;
use AdamczykPiotr\LaravelBalancedQueues\Services\Cpu\CpuCoreCountProvider;

$CPU_CORES = CpuCoreConfigurationResolver::CPU_CORES;

return [

    'defaults' => [
        'cpu_core_count' => 4, // CpuCoreCountProvider::class,
    ],

    'header' => [
        'supervisord' => [],
    ],

    'queues' => [
        JobWorkloadType::DEFAULT->value => 1,

        JobWorkloadType::CPU_HIGH->value => $CPU_CORES,
        JobWorkloadType::CPU_MEDIUM->value => 4 * $CPU_CORES,

        JobWorkloadType::NETWORK_HIGH_BANDWIDTH->value => 5,
        JobWorkloadType::NETWORK_HIGH_REQUESTS->value => 50,
    ],
];
