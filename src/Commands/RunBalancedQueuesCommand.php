<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Commands;

use AdamczykPiotr\LaravelBalancedQueues\Services\Supervisor\SupervisorConfigGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RunBalancedQueuesCommand extends Command
{
    public $signature = 'queue:run-balanced
        {--background : Run supervisor in daemon mode}
        {--path-only : Only print the generated config file path}';

    public $description = 'Command to run spawn supervisord process with balanced queues configuration';

    const string SUPERVISORD_BINARY = 'supervisord';

    const string CONFIG_FILE_PREFIX = 'supervisord-config-';

    public function handle(SupervisorConfigGenerator $supervisorConfigGenerator): int
    {
        $config = $supervisorConfigGenerator->generate();

        $directory = sys_get_temp_dir();
        $this->cleanup($directory);

        $configPath = tempnam($directory, static::CONFIG_FILE_PREFIX);
        File::put($configPath, $config);

        $pathOnly = $this->option('path-only');
        if ($pathOnly === true) {
            $this->line($configPath);

            return self::SUCCESS;
        }

        $daemonMode = $this->option('background') === true;

        $command = collect([
            static::SUPERVISORD_BINARY,
            ($daemonMode ? null : '--nodaemon'),
            '--configuration', $configPath,
        ])
            ->filter()
            ->join(' ');

        shell_exec($command);

        return self::SUCCESS;
    }

    protected function cleanup(string $directory): void
    {
        $prefix = static::CONFIG_FILE_PREFIX;
        $files = File::glob("{$directory}/{$prefix}*");
        foreach ($files as $file) {
            File::delete($file);
        }
    }
}
