<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Traits;

use AdamczykPiotr\LaravelBalancedQueues\Enums\JobWorkloadType;

trait HasHighNetworkBandwidthUsageQueue
{
    public string $queue = JobWorkloadType::NETWORK_HIGH_BANDWIDTH->value;
}
