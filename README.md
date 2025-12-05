# Laravel Balanced Queues

[![Latest Version on Packagist](https://img.shields.io/packagist/v/adamczykpiotr/laravel-balanced-queues.svg?style=flat-square)](https://packagist.org/packages/adamczykpiotr/laravel-balanced-queues)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/adamczykpiotr/laravel-balanced-queues/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/adamczykpiotr/laravel-balanced-queues/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/adamczykpiotr/laravel-balanced-queues/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/adamczykpiotr/laravel-balanced-queues/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/adamczykpiotr/laravel-balanced-queues.svg?style=flat-square)](https://packagist.org/packages/adamczykpiotr/laravel-balanced-queues)

**Stop guessing how many queue workers you need.** This package automatically optimizes worker counts based on job
type (CPU-intensive, API calls, file downloads) and your server's resources.

## Why You Need This

Running 10 workers for everything? **You're wasting resources or creating bottlenecks.**

- **Image downloads** saturate your network with too many workers
- **API calls** queue up with too few concurrent workers
- **CPU jobs** leave cores idle or cause context-switching overhead

Are you running your application on different machines? Different CPU core configurations, changing network bandwidth?
**With this package you can optimize it automatically and fine tune it for best results.**

## Quick Start

```bash
composer require adamczykpiotr/laravel-balanced-queues
php artisan vendor:publish --tag="balanced-queues-config"
```

Tag your jobs with the right workload type using traits:

```php
use AdamczykPiotr\LaravelBalancedQueues\Traits\HasBalancedQueues;

class ScrapeVideosJob implements ShouldQueue
{
    public function __construct() {
        $this->onHighNetworkBandwidthUsageQueu();
    }
}

class CallApiJob implements ShouldQueue
{
    public function __construct() {
        $this->onHighNetworkRequestUsageQueue();
    }
}

class ProcessBigDataJob implements ShouldQueue
{
    public function __construct() {
        $this->onHighCpuUsageQueue();
    }
}

class ProcessSmallerDataJob implements ShouldQueue
{
    public function __construct() {
        $this->onMediumCpuUsageQueue();
    }
}
```

Run your queues:

```bash
php artisan queue:run-balanced
```

**Done.** The package spawns all the queues with adjusted worker counts for each job type.

## Available helpers

| Helper method                      | Workload Type          | Use Case                                      |
|------------------------------------|------------------------|-----------------------------------------------|
| `onHighCpuUsageQueue`              | CPU_HIGH               | Large file processing ~MB/GB                  |
| `onMediumCpuUsageQueue`            | CPU_MEDIUM             | PDF generation, smaller file parsing (~KB/MB) |
| `onLowCpuUsageQueue`               | CPU_LOW                | Quick background queries                      |
| `onHighNetworkRequestUsageQueue`   | NETWORK_HIGH_BANDWIDTH | Large file downloads (~ >10MB)                |
| `onHighNetworkBandwidthUsageQueue` | NETWORK_HIGH_REQUESTS  | API calls, webhooks                           |
| `onFilesystemQueue`                | FILESYSTEM             | Filesystem operations (duh!)                  |

## How It Works

By default, `CPU_HIGH` usage assumes 100-50% CPU utilisation for a single job, ~50-25% for `CPU_MEDIUM` and ~10-20% for `CPU_LOW`.
Jobs on `NETWORK_HIGH_BANDWIDTH` are best suited for downloading large files that completely saturate network bandwidth
whereas `NETWORK_HIGH_REQUESTS` are in most cases are bottleneck by neither network nor cpu and are offloaded on a queue
simply to improve UX. `FILESYSTEM` jobs are assumed to be bottlenecked by disk I/O.

If the default configuration doesn't fully utilize your machine, adjust the following default configuration in
`config/balanced-queues.php`:

```php
use AdamczykPiotr\LaravelBalancedQueues\Enums\JobWorkloadType;

$CPU_CORES = CpuCoreConfigurationResolver::CPU_CORES;

return [
    'queues' => [
        JobWorkloadType::DEFAULT->value => 1,

        JobWorkloadType::CPU_HIGH->value => $CPU_CORES,
        JobWorkloadType::CPU_MEDIUM->value => 2 * $CPU_CORES,
        JobWorkloadType::CPU_LOW->value => 4 * $CPU_CORES,

        JobWorkloadType::NETWORK_HIGH_BANDWIDTH->value => 5,
        JobWorkloadType::NETWORK_HIGH_REQUESTS->value => 50,

        JobWorkloadType::FILESYSTEM->value => 2,
    ],
];
```

The default configuration aims to ensure best results across wide range of cases.
In order to get full benefits of this approach, it's *highly* recommended to fine-tune the configuration to *your*
specific use-case.
The clue to optimisation is to ensure a *single* queue can fully saturate the machine:

- Dispatching `CPU_CORES` jobs to `CPU_HIGH` queue should result in ~100% CPU usage
- Dispatching `2` * `CPU_CORES` jobs to `CPU_MEDIUM` queue should result in ~100% CPU usage
- Dispatching `4` * `CPU_CORES` jobs to `CPU_LOW` queue should result in ~100% CPU usage
- Dispatching `5` jobs to `NETWORK_HIGH_BANDWIDTH` queue should result in full network saturation
- Dispatching `50` jobs to `NETWORK_HIGH_REQUESTS` should result in jobs being finished as soon as possible (no real
  bottleneck)
- Dispatching `2` jobs to `FILESYSTEM` queue should result in full disk I/O saturation

In case more than one queue is fully saturated, OS scheduler will balance processes CPU time accordingly.

## Advanced Configuration

### Custom Queue Names

```php
'queues' => [
    'ml-training' => 2 * $CPU_CORES,
    'notifications' => 10,
    'web-scraping' => 25,
],
```

### VM / Docker CPU Limits

Override auto-detected CPU core count:

```php
'package' => [
    'cpu_core_count' => 4,  // Override auto-detected core count
],
```

### Worker Options

Add additional options to `artisan queue:work` command:

```php
'worker_options' => [
    '--timeout' => 60,
    '--tries' => 3,
    '--sleep' => 3,
],
```

### Supervisor Configuration

Customize the generated Supervisor configuration:

```php
'supervisor' => [
    'header' => [
        'user' => 'www-data',
        'numprocs' => 1,
    ],
],
```

## Commands

```bash
# Run queue workers
php artisan queue:run-balanced

# Run in background
php artisan queue:run-balanced --background

# Get path to generated config and run supervisor manually
php artisan queue:run-balanced --path-only
```

## Docker container entrypoint example

```
#!/bin/sh
set -e

SUPERVISORD_CONFIG_PATH=$(php artisan queue:run-balanced --path-only)
exec /usr/bin/supervisord -n -c "$SUPERVISORD_CONFIG_PATH"
```

## Requirements

- PHP 8.3+
- Laravel 11.x or 12.x
- Supervisor

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Piotr Adamczyk](https://github.com/adamczykpiotr)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
