<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Services\Supervisor;

use AdamczykPiotr\LaravelBalancedQueues\Services\Cpu\CpuCoreConfigurationResolver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SupervisorConfigGenerator
{
    public function __construct(
        protected CpuCoreConfigurationResolver $cpuCoreResolver,
    ) {}

    public function generate(): string
    {
        /** @var Collection<string, array<string, mixed>> $config */
        $config = collect((array) config('balanced-queues'));

        $workerOptions = $this->buildWorkerOptionString(
            collect($config->get('worker_options', []))
        );

        $queues = collect($config->get('queues', []))
            ->map(fn (float|int $coreCount, string $workloadType) => $this->generateConfigEntry(
                $workloadType,
                max($this->cpuCoreResolver->resolveCpuCores($coreCount), 1),
                $workerOptions
            ))
            ->join("\n\n");

        $header = $this->buildSupervsorHeader(
            collect($config->get('supervisor.header', []))
        );

        return "$header\n\n$queues\n";
    }

    protected function generateConfigEntry(string $workloadType, int $coreCount, string $optionString): string
    {
        $path = base_path("storage/logs/queue/{$workloadType}");
        if (File::exists($path) === false) {
            File::makeDirectory($path, recursive: true);
        }

        $executablePath = $this->getPhpExecutable();
        $artisanPath = $this->getArtisanPath();

        return "[program:queue-{$workloadType}]
process_name=%(program_name)s_%(process_num)03d
command={$executablePath} {$artisanPath} queue:work --queue={$workloadType} {$optionString}
autostart=true
autorestart=true
numprocs={$coreCount}
redirect_stderr=true
stdout_logfile={$path}/%(process_num)03d.log
stderr_logfile={$path}/%(process_num)03d_error.log";
    }

    /**
     * @param  Collection<string, array<string, string|int|float>|null>  $header
     */
    protected function buildSupervsorHeader(Collection $header): string
    {
        $requiredSectionName = 'supervisord';
        if ($header->has($requiredSectionName) === false) {
            $header->put($requiredSectionName, []);
        }

        $groups = $header->map(function (?array $items, string $groupName) {
            if ($items === null) {
                return null;
            }

            $items = collect($items)->map(fn ($value, $key) => "$key=$value")->implode("\n");

            return "[$groupName]\n$items";
        });

        return $groups->filter()->implode("\n\n");
    }

    protected function buildWorkerOptionString(Collection $options): string
    {
        return $options
            ->forget(['queue'])
            ->map(function (mixed $value, string|int $key) {
                $isFlag = is_int($key);
                if ($isFlag) {
                    return (string) $value;
                }

                return "$key=$value";
            })
            ->filter(fn (mixed $value) => Str::startsWith($value, '--'))
            ->implode(' ');
    }

    protected function getPhpExecutable(): string
    {
        return defined('PHP_BINARY')
            ? PHP_BINARY
            : 'php';
    }

    protected function getArtisanPath(): string
    {
        return base_path('artisan');
    }
}
