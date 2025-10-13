<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Services\Supervisor;

use AdamczykPiotr\LaravelBalancedQueues\Enums\JobWorkloadType;
use AdamczykPiotr\LaravelBalancedQueues\Services\Cpu\CpuCoreConfigurationResolver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class SupervisorConfigGenerator
{
    public function __construct(
        protected CpuCoreConfigurationResolver $cpuCoreResolver,
    ) {}

    public function generate(): string
    {
        /** @var Collection<string, array<string, mixed>> $config */
        $config = collect((array) config('balanced-queues'));

        $header = $this->buildHeader(
            collect($config->get('header', []))
        );

        $queues = collect($config->get('queues', []))
            ->map(fn (float|int $coreCount, string $workloadType) => $this->generateConfigEntry(
                JobWorkloadType::from($workloadType),
                max($this->cpuCoreResolver->resolveCpuCores($coreCount), 1)
            ))
            ->join("\n\n");

        return "$header\n\n$queues\n";
    }

    protected function generateConfigEntry(JobWorkloadType $workloadType, int $coreCount): string
    {
        $path = base_path("storage/logs/queue/{$workloadType->value}");
        if (File::exists($path) === false) {
            File::makeDirectory($path, recursive: true);
        }

        $executablePath = $this->getPhpExecutable();
        $artisanPath = $this->getArtisanPath();

        return "[program:queue-{$workloadType->value}]
process_name=%(program_name)s_%(process_num)03d
command={$executablePath} {$artisanPath} queue:work --queue={$workloadType->value}
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
    protected function buildHeader(Collection $header): string
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
