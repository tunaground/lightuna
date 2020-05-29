<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;

class MariadbBanDao extends AbstractBanDao implements BanDaoInterface
{
    public function getNextBanUid(): int
    {
        $sql = <<<SQL
select nextval(seq_ban_uid);
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