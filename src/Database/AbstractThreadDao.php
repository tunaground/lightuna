<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;
use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Object\Thread;

/**
 * Class AbstractThreadDao
 * @package Lightuna\Database
 */
abstract class AbstractThreadDao extends AbstractDao
{
    /**
     * @param int $threadUid
     * @return Thread
     * @throws DataAccessException
     */
    public function getThreadByThreadUid(int $threadUid): Thread
    {
        $sql = <<<SQL
select  *
from    thread
where   thread_uid = :thread_uid
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':thread_uid', $threadUid, \PDO::PARAM_INT);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
        if ($stmt->rowCount() !== 1) {
            throw new InvalidUserInputException('Invalid Thread UID.');
        }
        return $this->rawToObject($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * @param string $boardUid
     * @param int $limit
     * @param int $start
     * @return Thread[]
     * @throws DataAccessException
     */
    public function getThreadListByBoardUid(string $boardUid, int $limit, int $start = 0): array
    {
        $sql = <<<SQL
select  *
from    thread
where   board_uid = :board_uid
order by update_date desc
limit :start, :limit
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':board_uid', $boardUid, \PDO::PARAM_STR);
        $stmt->bindValue(':start', $start, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
        $rawThreads = ($stmt->rowCount() > 0) ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        return array_map(function ($rawThread) {
            return $this->rawToObject($rawThread);
        }, $rawThreads);
    }

    /**
     * @param int $threadUid
     * @return int
     * @throws DataAccessException
     */
    public function getThreadSize(int $threadUid): int
    {
        $sql = <<<SQL
select  count(*)
from    response
where   thread_uid = :thread_uid
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':thread_uid', $threadUid, \PDO::PARAM_STR);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
        return $stmt->fetchColumn();
    }

    /**
     * @param Thread $thread
     * @throws DataAccessException
     */
    public function createThread(Thread $thread)
    {
        $boardUid = $thread->getBoardUid();
        $sql = <<<SQL
insert into thread (thread_uid, board_uid, title, password, user_name, create_date, update_date)
values (:thread_uid, :board_uid, :title, :password, :user_name, :create_date, :update_date)
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':thread_uid', $thread->getThreadUid());
        $stmt->bindValue(':board_uid', $boardUid);
        $stmt->bindValue(':title', $thread->getTitle());
        $stmt->bindValue(':password', $thread->getPassword());
        $stmt->bindValue(':user_name', $thread->getUserName());
        $stmt->bindValue(':create_date', $thread->getCreateDate()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':update_date', $thread->getUpdateDate()->format('Y-m-d H:i:s'));
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
    }

    /**
     * @param int $threadUid
     * @return int
     * @throws DataAccessException
     */
    public function getLastResponseSequence(int $threadUid): int
    {
        $sql = <<<SQL
select  max(r.ms)
from   (select  max(sequence) as ms
        from    response
        where   thread_uid = :thread_uid
        union
        select  max(sequence) as ms
        from    arc_response
        where   thread_uid = :thread_uid) r
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':thread_uid', $threadUid, \PDO::PARAM_INT);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
        $sequence = $stmt->fetchColumn();
        if ($sequence === null) {
            return -1;
        } else {
            return $sequence;
        }
    }

    /**
     * @param int $threadUid
     * @param \DateTime $dateTime
     * @throws DataAccessException
     */
    public function setUpdateDate(int $threadUid, \DateTime $dateTime): void
    {
        $sql = <<<SQL
update  thread
set     update_date = :update_date
where   thread_uid = :thread_uid
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':update_date', $dateTime->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
        $stmt->bindValue(':thread_uid', $threadUid, \PDO::PARAM_INT);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException('Failed to query.');
        }
    }

    /**
     * @param array $rawThread
     * @return Thread
     */
    protected function rawToObject(array $rawThread): Thread
    {
        return new Thread(
            $rawThread['board_uid'],
            $rawThread['thread_uid'],
            $rawThread['title'],
            $rawThread['password'],
            $rawThread['user_name'],
            \DateTime::createFromFormat('Y-m-d H:i:s', $rawThread['create_date']),
            \DateTime::createFromFormat('Y-m-d H:i:s', $rawThread['update_date']),
        );
    }
}
