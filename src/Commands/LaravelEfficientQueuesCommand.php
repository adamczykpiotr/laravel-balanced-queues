<?php

namespace AdamczykPiotr\LaravelEfficientQueues\Commands;

use Illuminate\Console\Command;

class LaravelEfficientQueuesCommand extends Command
{
    public $signature = 'laravel-efficient-queues';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
