<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;
use Lightuna\Object\ArcResponse;
use Lightuna\Object\ResponseContent;

/**
 * Class AbstractArcResponseDao
 * @package Lightuna\Database
 */
abstract class AbstractArcResponseDao extends AbstractDao implements ArcResponseDaoInterface
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
                          youtube,
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
        :youtube,
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
        $stmt->bindValue(':youtube', $arcResponse->getYoutube(), \PDO::PARAM_STR);
        $stmt->bindValue(':archive_date', $arcResponse->getArchiveDate()->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
    }

    /**
     * @param int $sequence
     * @return ArcResponse
     * @throws DataAccessException
     */
    public function getArcResponseByResponseUid(int $responseUid): ArcResponse
    {
        $sql = <<<SQL
select  *
from    arc_response
where   response_uid = :response_uid
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':response_uid', $responseUid);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] === '00000' && $stmt->rowCount() === 1) {
            return $this->rawToObject($stmt->fetch(\PDO::FETCH_ASSOC));
        } else {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
    }

    /**
     * @param array $rawArcResponse
     * @return ArcResponse
     */
    protected function rawToObject(array $rawArcResponse): ArcResponse
    {
        $arcResponse = new ArcResponse(
            $rawArcResponse['arc_response_uid'],
            $rawArcResponse['thread_uid'],
            $rawArcResponse['response_uid'],
            $rawArcResponse['sequence'],
            $rawArcResponse['user_name'],
            $rawArcResponse['user_id'],
            $rawArcResponse['ip'],
            \DateTime::createFromFormat('Y-m-d H:i:s', $rawArcResponse['create_date']),
            new ResponseContent($rawArcResponse['content']),
            $rawArcResponse['attachment'],
            $rawArcResponse['youtube'],
            \DateTime::createFromFormat('Y-m-d H:i:s', $rawArcResponse['archive_date']),
        );
        return $arcResponse;
    }

    /**
     * @param int $arcResponseUid
     * @throws DataAccessException
     */
    public function deleteArcResponse(int $arcResponseUid)
    {
        $sql = <<<SQL
delete from arc_response
where arc_response_uid= :arc_response_uid
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':arc_response_uid', $arcResponseUid, \PDO::PARAM_INT);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
    }
}
