<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;
use Lightuna\Object\Response;
use Lightuna\Object\ResponseContent;

/**
 * Class AbstractResponseDao
 * @package Lightuna\Database
 */
abstract class AbstractResponseDao extends AbstractDao implements ResponseDaoInterface
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
     * @param int $threadUid
     * @param int $start
     * @param int $end
     * @return Response[]
     * @throws DataAccessException
     */
    public function getAllResponseListByThreadUid(int $threadUid, int $start, int $end): array
    {
        $sql = <<<SQL
select  *
from   (select  response_uid, thread_uid, sequence, user_name, user_id, ip, create_date, content, attachment, youtube, 0 as mask
        from    response
        where   thread_uid = :thread_uid
            and sequence >= :start
            and sequence <= :end
        union
        select  response_uid, thread_uid, sequence, user_name, user_id, ip, create_date, content, attachment, youtube, 1 as mask
        from    arc_response
        where   thread_uid = :thread_uid
            and sequence >= :start
            and sequence <= :end) r
order by sequence asc
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':thread_uid', $threadUid, \PDO::PARAM_INT);
        $stmt->bindValue(':start', $start, \PDO::PARAM_INT);
        $stmt->bindValue(':end', $end, \PDO::PARAM_INT);
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
     * @param int $threadUid
     * @param int $start
     * @param int $end
     * @return Response[]
     * @throws DataAccessException
     */
    public function getResponseListBySequence(int $threadUid, int $start, int $end): array
    {
        $sql = <<<SQL
select  *
from    response
where   thread_uid = :thread_uid
    and sequence >= :start
    and sequence <= :end
order by sequence asc
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':thread_uid', $threadUid, \PDO::PARAM_INT);
        $stmt->bindValue(':start', $start, \PDO::PARAM_INT);
        $stmt->bindValue(':end', $end, \PDO::PARAM_INT);
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
        if ($error[0] === '00000' && $stmt->rowCount() === 1) {
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
insert into response (response_uid, thread_uid, sequence, user_name, user_id, ip, create_date, content, attachment, youtube)
values (:response_uid, :thread_uid, :sequence, :user_name, :user_id, :ip, :create_date, :content, :attachment, :youtube)
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
        $stmt->bindValue(':youtube', $response->getYoutube(), \PDO::PARAM_STR);
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
     * @param string $userName
     * @param int $start
     * @param int $limit
     * @return Response[]
     * @throws DataAccessException
     */
    public function findByResponseUserName(string $userName, int $start, int $limit): array
    {
        $sql = <<<SQL
select  *
from    response
where   user_name = :keyword
limit   :start, :limit
SQL;
        try {
            return $this->findBy($sql, $userName, $start, $limit);
        } catch (DataAccessException $e) {
            throw new DataAccessException('Failed to query.');
        }
    }

    /**
     * @param string $userId
     * @param int $start
     * @param int $limit
     * @return Response[]
     * @throws DataAccessException
     */
    public function findByResponseUserId(string $userId, int $start, int $limit): array
    {
        $sql = <<<SQL
select  *
from    response
where   user_id = :keyword
limit   :start, :limit
SQL;
        try {
            return $this->findBy($sql, $userId, $start, $limit);
        } catch (DataAccessException $e) {
            throw new DataAccessException('Failed to query.');
        }
    }

    /**
     * @param string $sql
     * @param string $keyword
     * @prarm int $start
     * @param int $limit
     * @return Response[]
     * @throws DataAccessException
     */
    private function findBy(string $sql, string $keyword, $start, $limit)
    {
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':keyword', $keyword, \PDO::PARAM_STR);
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
     * @param array $rawResponse
     * @return Response
     */
    protected function rawToObject(array $rawResponse): Response
    {
        $response = new Response(
            $rawResponse['thread_uid'],
            $rawResponse['response_uid'],
            $rawResponse['sequence'],
            $rawResponse['user_name'],
            $rawResponse['user_id'],
            $rawResponse['ip'],
            \DateTime::createFromFormat('Y-m-d H:i:s', $rawResponse['create_date']),
            new ResponseContent($rawResponse['content']),
            $rawResponse['attachment'],
            $rawResponse['youtube']
        );
        if (isset($rawResponse['mask'])) {
            $response->setMask(($rawResponse['mask'] === '1'));
        };
        return $response;
    }
}
