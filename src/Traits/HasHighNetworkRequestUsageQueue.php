<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Traits;

use AdamczykPiotr\LaravelBalancedQueues\Enums\JobWorkloadType;

trait HasHighNetworkRequestUsageQueue
{
    public string $queue = JobWorkloadType::NETWORK_HIGH_REQUESTS->value;
}

