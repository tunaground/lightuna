<?php

namespace Lightuna\Dao;

use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\Response;

class MariadbResponseDao extends AbstractDao implements ResponseDaoInterface
{
    /**
     * @throws QueryException
     */
    public function getNextResponseId(): int
    {
        $sql = <<<SQL
select nextval(seq_response_id);
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        return $stmt->fetchColumn();
    }

    /**
     * @throws QueryException
     */
    public function createResponse(Response $response)
    {
        $sql = <<<SQL
insert into response(id, thread_id, sequence, username, user_id, ip, content, attachment, youtube, deleted, created_at)
values (:id, :thread_id, :sequence, :username, :user_id, :ip, :content, :attachment, :youtube, :deleted, :created_at)
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $response->getId());
        $stmt->bindValue(':thread_id', $response->getThreadId());
        $stmt->bindValue(':sequence', $response->getSequence());
        $stmt->bindValue(':username', $response->getUsername());
        $stmt->bindValue(':user_id', $response->getUserId());
        $stmt->bindValue(':ip', $response->getIp());
        $stmt->bindValue(':content', $response->getContent());
        $stmt->bindValue(':attachment', $response->getAttachment());
        $stmt->bindValue(':youtube', $response->getYoutube());
        $stmt->bindValue(':deleted', $response->getDeleted(), \PDO::PARAM_BOOL);
        $stmt->bindValue(':created_at', $response->getCreatedAt()->format(DATETIME_FORMAT));
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
    }

    /**
     * @param int $threadId
     * @return Response[]
     * @throws QueryException
     * @throws ResourceNotFoundException
     */
    public function getReponsesByThreadId(int $threadId): array
    {
        $sql = <<<SQL
select id, thread_id, sequence, username, user_id, ip, content, attachment, youtube, deleted, created_at, deleted_at
from response
where thread_id = :thread_id
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':thread_id', $threadId);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        if ($stmt->rowCount() === 0) {
            throw new ResourceNotFoundException("thread($threadId) not exists");
        }
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_reduce($results, function ($acc, $result) {
            $acc[] = $this->makeObject($result);
            return $acc;
        }, []);
    }

    /**
     * @param int $threadId
     * @return int
     * @throws QueryException
     * @throws ResourceNotFoundException
     */
    public function getResponsesCountByThreadId(int $threadId): int
    {
        $sql = <<<SQL
select count(*)
from response
where thread_id = :thread_id
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':thread_id', $threadId);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        if ($stmt->rowCount() === 0) {
            throw new ResourceNotFoundException("thread($threadId) not exists");
        }
        return $stmt->fetchColumn();
    }

    private function makeObject(array $result): Response
    {
        return new Response(
            $result['id'],
            $result['thread_id'],
            $result['sequence'],
            $result['username'],
            $result['user_id'],
            $result['ip'],
            $result['content'],
            $result['attachment'],
            $result['youtube'],
            $result['deleted'],
            \DateTime::createFromFormat(DATETIME_FORMAT, $result['created_at']),
            ($result['deleted_at'] === null)
                ? null
                : \DateTime::createFromFormat(DATETIME_FORMAT, $result['deleted_at']),
        );
    }
}
