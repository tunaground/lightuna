<?php

namespace Lightuna\Dao;

use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\Thread;

class MariadbThreadDao extends AbstractDao implements ThreadDaoInterface
{
    /**
     * @throws QueryException
     */
    public function getNextThreadId(): int
    {
        $sql = <<<SQL
select nextval(seq_thread_id);
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
    public function createThread(Thread $thread)
    {
        $sql = <<<SQL
insert into thread (id, board_id, title, password, username, ended, created_at, updated_at)
values (:thread_id, :board_id, :title, :password, :username, :ended, :created_at, :updated_at)
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':thread_id', $thread->getId());
        $stmt->bindValue(':board_id', $thread->getBoardId());
        $stmt->bindValue(':title', $thread->getTitle());
        $stmt->bindValue(':password', $thread->getTitle());
        $stmt->bindValue(':username', $thread->getUsername());
        $stmt->bindValue(':ended', $thread->isEnded(), \PDO::PARAM_BOOL);
        $stmt->bindValue(':created_at', $thread->getCreatedAt()->format(DATETIME_FORMAT));
        $stmt->bindValue(':updated_at', $thread->getUpdatedAt()->format(DATETIME_FORMAT));
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
    }

    /**
     * @return Thread[]
     * @throws QueryException
     */
    public function getThreadByBoardId(string $boardId, int $limit = 0, int $offset = 0): array
    {
        $sql = <<<SQL
select id, board_id, title, password, username, ended, created_at, updated_at, deleted_at
from thread
where board_id = :board_id
order by updated_at desc
limit :limit
offset :offset
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':board_id', $boardId);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_reduce($results, function ($acc, $result) {
            $acc[] = $this->makeObject($result);
            return $acc;
        }, []);
    }

    public function getThreadById(int $id): Thread
    {
        $sql = <<<SQL
select * from thread where id = :id
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        if ($stmt->rowCount() === 0) {
            throw new ResourceNotFoundException("thread($id) not exists");
        }
        return $this->makeObject($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    private function makeObject(array $result): Thread
    {
        return new Thread(
            $result['id'],
            $result['board_id'],
            $result['title'],
            $result['password'],
            $result['username'],
            $result['ended'],
            \DateTime::createFromFormat(DATETIME_FORMAT, $result['created_at']),
            \DateTime::createFromFormat(DATETIME_FORMAT, $result['updated_at']),
            ($result['deleted_at'] === null)
                ? null
                : \DateTime::createFromFormat(DATETIME_FORMAT, $result['deleted_at']),
        );

    }
}
