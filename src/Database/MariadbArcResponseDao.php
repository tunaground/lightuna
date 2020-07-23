<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;

/**
 * Class MariadbArcResponseDao
 * @package Lightuna\Database
 */
class MariadbArcResponseDao extends AbstractArcResponseDao implements ArcResponseDaoInterface
{
    /**
     * @return int
     * @throws DataAccessException
     */
    public function getNextArcResponseUid(): int
    {
        $sql = <<<SQL
select nextval(seq_arc_response_uid);
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException(MSG_QUERY_FAILED);
        }
        return $stmt->fetchColumn();
    }
}
