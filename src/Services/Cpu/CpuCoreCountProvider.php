<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Services\Cpu;

class CpuCoreCountProvider implements CpuCoreCountProviderContract
{
    protected static ?int $cpuCoreCount = null;

    public function getCpuCoreCount(): int
    {
        if (self::$cpuCoreCount !== null) {
            return self::$cpuCoreCount;
        }

        $command = match (true) {
            PHP_OS_FAMILY == 'Windows' => 'echo %NUMBER_OF_PROCESSORS%',
            default => 'nproc',
        };

        $output = shell_exec($command);
        self::$cpuCoreCount = (int) $output;

        return self::$cpuCoreCount;
    }
}
