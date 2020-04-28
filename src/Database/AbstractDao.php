<?php
namespace Lightuna\Database;

use Lightuna\Log\Logger;
use Lightuna\Log\SetLoggerTrait;

/**
 * Class AbstractDao
 * @package Lightuna\Database
 * @property Logger $logger
 */
abstract class AbstractDao
{
    use SetLoggerTrait;

    /** @var DataSource */
    protected $dataSource;

    /**
     * AbstractDao constructor.
     * @param DataSource $dataSource
     * @param Logger $logger
     */
    public function __construct(DataSource $dataSource, Logger $logger)
    {
        $this->dataSource = $dataSource;
        $this->logger = $logger;
    }

    /**
     * @param string $method
     * @param string $message
     */
    public function logQueryError(string $method, string $message)
    {
        $this->logger->error(
            'Failed to execute query. {method}: {msg}',
            ['method' => $method, 'msg' => $message]
        );
    }
}