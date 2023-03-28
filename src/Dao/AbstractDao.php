<?php

namespace Lightuna\Dao;

abstract class AbstractDao
{
    public function __construct(protected \PDO $pdo) {}
}