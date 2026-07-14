<?php

use AdamczykPiotr\LaravelBalancedQueues\Services\Cpu\CpuCoreConfigurationResolver;
use AdamczykPiotr\LaravelBalancedQueues\Services\Supervisor\SupervisorConfigGenerator;

beforeEach(function () {
    config([
        'balanced-queues.queues' => [
            'default' => 1,
        ],
        'balanced-queues.worker_options' => [],
        'balanced-queues.supervisor.header' => [],
    ]);
});

it('generates supervisor config with signal handling options', function () {
    config([
        'balanced-queues.supervisor.stopsignal' => 'SIGTERM',
        'balanced-queues.supervisor.stopwaitsecs' => 605,
        'balanced-queues.supervisor.stopasgroup' => true,
        'balanced-queues.supervisor.killasgroup' => true,
    ]);

    $resolver = app(CpuCoreConfigurationResolver::class);
    $generator = new SupervisorConfigGenerator($resolver);
    $config = $generator->generate();

    expect($config)->toContain('stopsignal=SIGTERM');
    expect($config)->toContain('stopwaitsecs=605');
    expect($config)->toContain('stopasgroup=true');
    expect($config)->toContain('killasgroup=true');
});

it('generates supervisor config with partial signal handling options', function () {
    config([
        'balanced-queues.supervisor.stopsignal' => 'SIGTERM',
        'balanced-queues.supervisor.stopwaitsecs' => null,
        'balanced-queues.supervisor.stopasgroup' => true,
        'balanced-queues.supervisor.killasgroup' => null,
    ]);

    $resolver = app(CpuCoreConfigurationResolver::class);
    $generator = new SupervisorConfigGenerator($resolver);
    $config = $generator->generate();

    expect($config)->toContain('stopsignal=SIGTERM');
    expect($config)->not->toContain('stopwaitsecs=');
    expect($config)->toContain('stopasgroup=true');
    expect($config)->not->toContain('killasgroup=');
});

it('generates supervisor config without signal handling options when all are null', function () {
    config([
        'balanced-queues.supervisor.stopsignal' => null,
        'balanced-queues.supervisor.stopwaitsecs' => null,
        'balanced-queues.supervisor.stopasgroup' => null,
        'balanced-queues.supervisor.killasgroup' => null,
    ]);

    $resolver = app(CpuCoreConfigurationResolver::class);
    $generator = new SupervisorConfigGenerator($resolver);
    $config = $generator->generate();

    expect($config)->not->toContain('stopsignal=');
    expect($config)->not->toContain('stopwaitsecs=');
    expect($config)->not->toContain('stopasgroup=');
    expect($config)->not->toContain('killasgroup=');
});

it('generates supervisor config with stopasgroup and killasgroup set to false', function () {
    config([
        'balanced-queues.supervisor.stopsignal' => null,
        'balanced-queues.supervisor.stopwaitsecs' => null,
        'balanced-queues.supervisor.stopasgroup' => false,
        'balanced-queues.supervisor.killasgroup' => false,
    ]);

    $resolver = app(CpuCoreConfigurationResolver::class);
    $generator = new SupervisorConfigGenerator($resolver);
    $config = $generator->generate();

    expect($config)->toContain('stopasgroup=false');
    expect($config)->toContain('killasgroup=false');
});
