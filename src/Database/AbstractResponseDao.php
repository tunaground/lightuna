<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;
use Lightuna\Object\Response;
use Lightuna\Object\ResponseContent;

/**
 * Class AbstractResponseDao
 * @package Lightuna\Database
 */
abstract class AbstractResponseDao extends AbstractDao
{
    /**
     * @param int $threadUid
     * @param int $start
     * @param int $limit
     * @return Response[]
     * @throws DataAccessException
     */
    public function getResponseListByThreadUid(int $threadUid, int $start, int $limit): array
    {
        $sql = <<<SQL
select  *
from    response
where   thread_uid = :thread_uid
order by sequence asc
limit   :start, :limit
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':thread_uid', $threadUid, \PDO::PARAM_INT);
        $stmt->bindValue(':start', $start, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
        $rawResponses = ($stmt->rowCount() > 0) ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        return array_map(function ($rawResponse) {
            return $this->rawToObject($rawResponse);
        }, $rawResponses);
    }

    /**
     * @param int $responseUid
     * @return Response
     * @throws DataAccessException
     */
    public function getResponseByResponseUid(int $responseUid): Response
    {
        $sql = <<<SQL
select  *
from    response
where   response_uid = :response_uid
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':response_uid', $responseUid, \PDO::PARAM_INT);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if (
            $error[0] !== '00000' ||
            $stmt->rowCount() === 1
        ) {
            return $this->rawToObject($stmt->fetch(\PDO::FETCH_ASSOC));
        } else {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
    }

    /**
     * @param Response $response
     * @throws DataAccessException
     */
    public function createResponse(Response $response)
    {
        $sql = <<<SQL
insert into response (response_uid, thread_uid, sequence, user_name, user_id, ip, create_date, content, attachment)
values (:response_uid, :thread_uid, :sequence, :user_name, :user_id, :ip, :create_date, :content, :attachment)
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':response_uid', $response->getResponseUid(), \PDO::PARAM_INT);
        $stmt->bindValue(':thread_uid', $response->getThreadUid(), \PDO::PARAM_INT);
        $stmt->bindValue(':sequence', $response->getSequence(), \PDO::PARAM_INT);
        $stmt->bindValue(':user_name', $response->getUserName(), \PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $response->getUserId(), \PDO::PARAM_STR);
        $stmt->bindValue(':ip', $response->getIp(), \PDO::PARAM_STR);
        $stmt->bindValue(':create_date', $response->getCreateDate()->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
        $stmt->bindValue(':content', $response->getContent(), \PDO::PARAM_STR);
        $stmt->bindValue(':attachment', $response->getAttachment(), \PDO::PARAM_STR);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
    }

    /**
     * @param int $responseUid
     * @throws DataAccessException
     */
    public function deleteResponse(int $responseUid)
    {
        $sql = <<<SQL
delete from response
where response_uid = :response_uid
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':response_uid', $responseUid, \PDO::PARAM_INT);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
    }

    /**
     * @param array $rawResponse
     * @return Response
     */
    protected function rawToObject(array $rawResponse): Response
    {
        return new Response(
            $rawResponse['thread_uid'],
            $rawResponse['response_uid'],
            $rawResponse['sequence'],
            $rawResponse['user_name'],
            $rawResponse['user_id'],
            $rawResponse['ip'],
            \DateTime::createFromFormat('Y-m-d H:i:s', $rawResponse['create_date']),
            new ResponseContent($rawResponse['content']),
            $rawResponse['attachment'],
        );
    }
}
