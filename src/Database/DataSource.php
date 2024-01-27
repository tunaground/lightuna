<?php

namespace Lightuna\Database;

use Lightuna\Exception\InvalidConfigException;

class DataSource
{
    public static function getPdo(array $databaseConfig): \PDO
    {
        switch ($databaseConfig['type']) {
            case 'mariadb':
                $pdo = new \PDO(
                    sprintf(
                        'mysql:host=%s;port=%s;dbname=%s',
                        $databaseConfig['host'],
                        $databaseConfig['port'],
                        $databaseConfig['schema'],
                    ),
                    $databaseConfig['user'],
                    $databaseConfig['password'],
                    $databaseConfig['options'],
                );
                break;
            default:
                throw new InvalidConfigException();
        }
        return $pdo;
    }
}

