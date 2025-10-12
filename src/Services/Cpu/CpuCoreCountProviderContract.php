<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Services\Cpu;

interface CpuCoreCountProviderContract
{
    public function getCpuCoreCount(): int;
}
