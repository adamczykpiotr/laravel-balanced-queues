<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Services\Cpu;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;

class CpuCoreConfigurationResolver
{
    /**
     * This constant has been declared to provide a cleaner and stateless configuration file.
     * It doesn't represent the actual number of CPU cores and is resolved during runtime.
     */
    const float CPU_CORES = M_PI;

    /**
     * @throws BindingResolutionException
     */
    public function resolveCpuCores(float|int $value): int
    {
        // Absolute value
        if (is_int($value)) {
            return $value;
        }

        // Round value (i.e. 3.0, 2.25)
        $decimalPlaces = Str::of((string) $value)->after('.')->length();
        if (is_float($value) && $decimalPlaces < 3) {
            return (int) round($value);
        }

        // Relative to CPU_CORES
        $relative = $value / self::CPU_CORES;

        return (int) round($relative * $this->getCpuCoreCount());
    }

    /**
     * @throws BindingResolutionException
     */
    public function getCpuCoreCount(): int
    {
        $resolver = config('balanced-queues.defaults.cpu_core_count');

        // Pre-defined number of CPU cores
        if (is_numeric($resolver)) {
            return (int) $resolver;
        }

        $resolverInstance = app()->make($resolver);
        assert($resolverInstance instanceof CpuCoreCountProviderContract);

        return $resolverInstance->getCpuCoreCount();
    }
}
