<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Services\Cpu;

use Illuminate\Support\Str;

class CpuCoreResolver
{
    /**
     * This constant has been declared to provide a cleaner and stateless configuration file.
     * It doesn't represent the actual number of CPU cores and is resolved during runtime.
     */
    const float CPU_CORES = M_PI;
    protected static ?int $cpuCoreCount = null;


    /**
     * @param float|int $value
     * @return int
     */
    public function resolveCpuCores(float|int $value): int
    {
        // Absolute value
        if (is_int($value)) {
            return $value;
        }

        // Round value (i.e. 3.0, 2.25)
        $decimalPlaces = Str::of((string)$value)->after('.')->length();
        if (is_float($value) && $decimalPlaces < 3) {
            return (int)round($value);
        }

        // Relative to CPU_CORES
        $relative = $value / self::CPU_CORES;
        return (int)round($relative * $this->getCpuCoreCount());
    }


    /**
     * @return int
     */
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
        self::$cpuCoreCount = (int)$output;
        return self::$cpuCoreCount;
    }
}
