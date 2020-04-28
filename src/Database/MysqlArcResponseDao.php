<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;

/**
 * Class MysqlArcResponseDao
 * @package Lightuna\Database
 */
class MysqlArcResponseDao extends AbstractArcResponseDao implements ArcResponseDaoInterface
{
    /**
     * @return int
     * @throws DataAccessException
     */
    public function getNextArcResponseUid(): int
    {
        $sql = <<<SQL
insert into seq_arc_response_uid(sequence) values (0);
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
