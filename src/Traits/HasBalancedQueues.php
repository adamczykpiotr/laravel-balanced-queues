<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Traits;

use AdamczykPiotr\LaravelBalancedQueues\Enums\JobWorkloadType;
use Illuminate\Bus\Queueable;

trait HasBalancedQueues
{
    use Queueable;

    public function onDefaultQueue(): static
    {
        return $this->onQueue(JobWorkloadType::DEFAULT->value);
    }

    public function onFilesystemQueue(): static
    {
        return $this->onQueue(JobWorkloadType::FILESYSTEM->value);
    }

    public function onHighCpuUsageQueue(): static
    {
        return $this->onQueue(JobWorkloadType::CPU_HIGH->value);
    }

    public function onMediumCpuUsageQueue(): static
    {
        return $this->onQueue(JobWorkloadType::CPU_MEDIUM->value);
    }

    public function onLowCpuUsageQueue(): static
    {
        return $this->onQueue(JobWorkloadType::CPU_LOW->value);
    }

    public function onHighNetworkRequestUsageQueue(): static
    {
        return $this->onQueue(JobWorkloadType::NETWORK_HIGH_REQUESTS->value);
    }

    public function onHighNetworkBandwidthUsageQueue(): static
    {
        return $this->onQueue(JobWorkloadType::NETWORK_HIGH_BANDWIDTH->value);
    }
}
