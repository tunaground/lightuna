<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;

/**
 * Class MysqlThreadDao
 * @package Lightuna\Database
 */
class MysqlThreadDao extends AbstractThreadDao implements ThreadDaoInterface
{
    /**
     * @return int
     * @throws DataAccessException
     */
    public function getNextThreadUid(): int
    {
        $sql = <<<SQL
insert into seq_thread_uid(sequence) values (0);
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
        $sql = <<<SQL
select last_insert_id();
SQL;
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
        return $stmt->fetchColumn();
    }
}
