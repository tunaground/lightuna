<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;
use Lightuna\Object\ArcResponse;

/**
 * Class ArcResponseDao
 * @package Lightuna\Database
 */
class ArcResponseDao extends AbstractDao
{
    /**
     * @param ArcResponse $arcResponse
     * @throws DataAccessException
     */
    public function createArcResponse(ArcResponse $arcResponse): void
    {
        $sql = <<<SQL
insert into arc_response (arc_response_uid,
                          response_uid,
                          thread_uid,
                          sequence,
                          user_name,
                          user_id,
                          ip,
                          create_date,
                          content,
                          attachment,
                          archive_date)
values (:arc_response_uid,
        :response_uid,
        :thread_uid,
        :sequence,
        :user_name,
        :user_id,
        :ip,
        :create_date,
        :content,
        :attachment,
        :archive_date)
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':arc_response_uid', $arcResponse->getArcResponseUid(), \PDO::PARAM_INT);
        $stmt->bindValue(':response_uid', $arcResponse->getResponseUid(), \PDO::PARAM_INT);
        $stmt->bindValue(':thread_uid', $arcResponse->getThreadUid(), \PDO::PARAM_INT);
        $stmt->bindValue(':sequence', $arcResponse->getSequence(), \PDO::PARAM_INT);
        $stmt->bindValue(':user_name', $arcResponse->getUserName(), \PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $arcResponse->getUserId(), \PDO::PARAM_STR);
        $stmt->bindValue(':ip', $arcResponse->getIp(), \PDO::PARAM_STR);
        $stmt->bindValue(':create_date', $arcResponse->getCreateDate()->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
        $stmt->bindValue(':content', $arcResponse->getContent(), \PDO::PARAM_STR);
        $stmt->bindValue(':attachment', $arcResponse->getAttachment(), \PDO::PARAM_STR);
        $stmt->bindValue(':archive_date', $arcResponse->getArchiveDate()->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
    }

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
            throw new DataAccessException('Failed to query.');
        }
        return $stmt->fetchColumn();
    }
}