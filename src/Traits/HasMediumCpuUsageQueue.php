<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Traits;

use AdamczykPiotr\LaravelBalancedQueues\Enums\JobWorkloadType;

trait HasMediumCpuUsageQueue
{
    public string $queue = JobWorkloadType::CPU_MEDIUM->value;
}
