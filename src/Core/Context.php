<?php

namespace Lightuna\Core;

use Lightuna\Database\DataSource;
use Lightuna\Http\HttpResponse;

class Context
{
    private array $config;
    private \PDO $pdo;
    private array $argument;
    private HttpResponse $response;

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setArgument(array $argument): void
    {
        $this->argument = $argument;
    }

    public function setResponse(HttpResponse $response): void
    {
        $this->response = $response;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function getArgument(): array
    {
        return $this->argument;
    }

    public function getResponse(): HttpResponse
    {
        return $this->response;
    }
}

