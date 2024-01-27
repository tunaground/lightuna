<?php

namespace Lightuna\Core;

use Lightuna\Database\DataSource;
use Lightuna\Http\HttpResponse;

class Context
{
    private array $config;
    private array $argument;

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function setArgument(array $argument): void
    {
        $this->argument = $argument;
    }


    public function getConfig(): array
    {
        return $this->config;
    }

    public function getArgument(): array
    {
        return $this->argument;
    }
}