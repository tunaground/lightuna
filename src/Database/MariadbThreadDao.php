<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;

/**
 * Class MariadbThreadDao
 * @package Lightuna\Database
 */
class MariadbThreadDao extends AbstractThreadDao implements ThreadDaoInterface
{
    /**
     * @return int
     * @throws DataAccessException
     */
    public function getNextThreadUid(): int
    {
        $sql = <<<SQL
select nextval(seq_thread_uid);
SQL;
        $conn = $this->dataSource->getConnection();
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
