<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Traits;

use AdamczykPiotr\LaravelBalancedQueues\Enums\JobWorkloadType;

trait HasHighCpuUsageQueue
{
    public string $queue = JobWorkloadType::CPU_HIGH->value;
}

