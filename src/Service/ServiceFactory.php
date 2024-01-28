<?php

namespace Lightuna\Service;

use Lightuna\Controller\ControllerFactory;
use Lightuna\Dao\DaoFactory;
use Lightuna\Dao\MariadbResponseDao;
use Lightuna\Database\DataSource;

class ServiceFactory
{
    public static function getBoardService(array $config): BoardServiceInterface
    {
        $databaseType = $config['database']['type'];
        $pdo = DataSource::getPdo($config['database']);

        return new BoardService(
            DaoFactory::getBoardDao($databaseType, $pdo),
            DaoFactory::getNoticeDao($databaseType, $pdo),
        );
    }

    public static function getThreadService(array $config): ThreadServiceInterface
    {
        $databaseType = $config['database']['type'];
        $pdo = DataSource::getPdo($config['database']);

        return new ThreadService(
            DaoFactory::getThreadDao($databaseType, $pdo),
            DaoFactory::getResponseDao($databaseType, $pdo),
        );
    }

    public static function getAttachmentService(array $config): AttachmentServiceInterface
    {
        return new AttachmentService($config);
    }
}