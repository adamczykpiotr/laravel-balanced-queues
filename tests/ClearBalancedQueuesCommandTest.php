<?php

use AdamczykPiotr\LaravelBalancedQueues\Commands\ClearBalancedQueuesCommand;

it('clears all configured queues', function () {
    config()->set('balanced-queues.queues', [
        'default' => 1,
        'high' => 2,
    ]);

    $this->artisan(ClearBalancedQueuesCommand::class)
        ->assertSuccessful();
});

it('succeeds when no queues are configured', function () {
    config()->set('balanced-queues.queues', []);

    $this->artisan(ClearBalancedQueuesCommand::class)
        ->assertSuccessful();
});
