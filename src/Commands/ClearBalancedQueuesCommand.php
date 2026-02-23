<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Commands;

use Illuminate\Console\Command;

class ClearBalancedQueuesCommand extends Command
{
    public $signature = 'queue:clear-balanced';

    public $description = 'Clear all balanced queues';

    public function handle(): int
    {
        $queues = array_keys(config('balanced-queues.queues', []));

        foreach ($queues as $queue) {
            $this->call('queue:clear', ['--queue' => $queue]);
        }

        return self::SUCCESS;
    }
}
