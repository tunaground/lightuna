<?php

namespace Lightuna\Core;

use Lightuna\Database\DataSource;
use Lightuna\Http\HttpResponse;

class Context
{
    private \PDO $pdo;
    private array $argument;
    private HttpResponse $response;

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

