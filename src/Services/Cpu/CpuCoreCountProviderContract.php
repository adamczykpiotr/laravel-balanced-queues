<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Services\Cpu;

interface CpuCoreCountProviderContract
{

    /**
     * @return int
     */
    public function getCpuCoreCount(): int;
}
