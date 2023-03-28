<?php

namespace Lightuna\Database;

interface DataSourceInterface
{
    public function getConnection(): \PDO;
}

