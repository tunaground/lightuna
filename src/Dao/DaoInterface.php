<?php

namespace Lightuna\Dao;

interface DaoInterface
{
    public function getPdo(): \PDO;

    public function setPdo(\PDO $pdo): void;
}