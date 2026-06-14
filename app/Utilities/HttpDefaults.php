<?php

declare(strict_types=1);

namespace App\Utilities;

final readonly class HttpDefaults
{
    public const string USER_AGENT = 'Heartbeat-Lab/1.0';

    public const string ACCEPT = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';

    public const int HTTP_TIMEOUT = 10;

    public const int CONNECT_TIMEOUT = 5;

    public const int RETRY_TIMES = 3;

    public const int RETRY_DELAY = 100;
}
