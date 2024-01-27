<?php

namespace Lightuna\Dao;

use Lightuna\Exception\InvalidConfigException;

class DaoFactory
{
    public static function getBoardDao(string $type, \PDO $pdo): BoardDaoInterface
    {
        switch ($type) {
            case 'mariadb':
                return new MariadbBoardDao($pdo);
            default:
                throw new InvalidConfigException();
        }
    }

    public static function getThreadDao(string $type, \PDO $pdo): ThreadDaoInterface
    {
        switch ($type) {
            case 'mariadb':
                return new MariadbThreadDao($pdo);
            default:
                throw new InvalidConfigException();
        }
    }

    public static function getResponseDao(string $type, \PDO $pdo): ResponseDaoInterface
    {
        switch ($type) {
            case 'mariadb':
                return new MariadbResponseDao($pdo);
            default:
                throw new InvalidConfigException();
        }
    }
}