<?php

namespace Lightuna\Core;

use Lightuna\Database\DataSource;
use Lightuna\Http\HttpResponse;
use Lightuna\Log\Log;
use Lightuna\Log\Logger;

class Context
{
    private array $config;
    private Logger $logger;
    private array $argument;

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function setArgument(array $argument): void
    {
        $this->argument = $argument;
    }

    public function setLogger(Logger $logger): void
    {
        $this->logger = $logger;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getArgument(): array
    {
        return $this->argument;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }
}