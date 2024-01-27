<?php

namespace Lightuna\Dao;

abstract class AbstractDao implements DaoInterface
{
    public function __construct(protected \PDO $pdo) {}

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function setPdo(\PDO $pdo): void
    {
        $this->pdo = $pdo;
    }
}