<?php
namespace Lightuna\Database;

use Lightuna\Log\Logger;
use Lightuna\Log\SetLoggerTrait;

/**
 * Class DataSource
 * @package Lightuna\Database
 * @property Logger $logger
 */
class DataSource
{
    use SetLoggerTrait;

    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var string */
    private $user;
    /** @var string */
    private $password;
    /** @var string */
    private $schema;
    /** @var array */
    private $options;
    /** @var \PDO */
    private $transaction;

    /**
     * DataSource constructor.
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $password
     * @param string $schema
     * @param array $options
     * @param Logger $logger
     */
    public function __construct(
        string $host,
        int $port,
        string $user,
        string $password,
        string $schema,
        array $options,
        Logger $logger
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->schema = $schema;
        $this->options = $options;
        $this->logger = $logger;
    }

    /**
     * @return \PDO
     * @throws \PDOException
     */
    public function getConnection(): \PDO
    {
        try {
            if ($this->isTransaction()) {
                return $this->transaction;
            } else {
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
        } catch (\PDOException $e) {
            $this->logger->error(
                'Failed to connect to data source. {msg}',
                ['msg' => $e->getMessage()]
            );
            throw $e;
        }
    }

    /**
     * @return bool
     */
    public function isTransaction(): bool
    {
        if ($this->transaction instanceof \PDO) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @throws \PDOException
     */
    public function beginTransaction(): void
    {
        try {
            $connection = $this->getConnection();
            $connection->beginTransaction();
            $this->transaction = $connection;
        } catch (\PDOException $e) {
            $this->logger->error(
                'Failed to begin transaction. {msg}',
                ['msg' => $e->getMessage()]
            );
            throw $e;
        }
    }

    /**
     * @throws \PDOException
     */
    public function commit(): void
    {
        try {
            $this->transaction->commit();
        } catch (\PDOException $e) {
            $this->logger->error(
                'Failed to commit transaction. {msg}',
                ['msg', $e->getMessage()]
            );
            throw $e;
        }
    }

    /**
     * @throws \PDOException
     */
    public function rollBack(): void
    {
        try {
            $this->transaction->rollBack();
        } catch (\PDOException $e) {
            $this->logger->error(
                'Failed to rollback transaction. {msg}',
                ['msg' => $e->getMessage()]
            );
            throw $e;
        }
    }
}
