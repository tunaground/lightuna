<?php

namespace Lightuna\Database;

class Mariadb implements DataSourceInterface
{
    private string $host;
    private int $port;
    private string $user;
    private string $password;
    private string $schema;
    private array $options;

    public function __construct(
        string $host,
        int    $port,
        string $user,
        string $password,
        string $schema,
        array  $options,
    )
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->schema = $schema;
        $this->options = $options;
    }

    public function getConnection(): \PDO
    {
        return new \PDO(
            sprintf(
                'mysql:host=%s;port=%s;dbname=%s',
                $this->host,
                $this->port,
                $this->schema
            ),
            $this->user,
            $this->password,
            $this->options
        );
    }
}

