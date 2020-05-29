<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;

class MysqlBanDao extends AbstractBanDao implements BanDaoInterface
{
    public function getNextBanUid(): int
    {
        $sql = <<<SQL
insert into seq_ban_uid(sequence) values (0);
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