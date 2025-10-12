<?php

namespace AdamczykPiotr\LaravelBalancedQueues\Enums;

enum JobWorkloadType: string
{
    case DEFAULT = 'default';

    case CPU_HIGH = 'cpu-high';
    case CPU_MEDIUM = 'cpu-medium';

    case NETWORK_HIGH_BANDWIDTH = 'network-high-bandwidth';
    case NETWORK_HIGH_REQUESTS = 'network-high-requests';
}
