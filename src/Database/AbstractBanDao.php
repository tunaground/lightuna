<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;
use Lightuna\Object\Ban;

abstract class AbstractBanDao extends AbstractDao implements BanDaoInterface
{
    /**
     * @param Ban $ban
     * @throws DataAccessException
     */
    public function createBan(Ban $ban): void
    {
        $sql = <<<SQL
insert into ban (ban_uid, thread_uid, user_id, ip, issue_date)
values (:ban_uid, :thread_uid, :user_id, :ip, :issue_date)
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':ban_uid', $ban->getBanUid(), \PDO::PARAM_INT);
        $stmt->bindValue(':thread_uid', $ban->getThreadUid(), \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $ban->getUserId(), \PDO::PARAM_STR);
        $stmt->bindValue(':ip', $ban->getIp(), \PDO::PARAM_STR);
        $stmt->bindValue(':issue_date', $ban->getIssueDate()->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException(MSG_QUERY_FAILED);
        }
    }

    /**
     * @param int $threadUid
     * @param string $userId
     * @param \DateTime $dateTime
     * @return bool
     * @throws DataAccessException
     */
    public function checkBanStatus(int $threadUid, string $userId, \DateTime $dateTime): bool
    {
        $sql = <<<SQL
select  count(*)
from    ban
where   thread_uid = :thread_uid
    and user_id = :user_id
    and date_format(issue_date, '%Y%m%d') = :issue_date
SQL;
        $conn = $this->dataSource->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':thread_uid', $threadUid, \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_STR);
        $stmt->bindValue(':issue_date', $dateTime->format('Ymd'), \PDO::PARAM_STMT);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            $this->logQueryError(__METHOD__, $error[2]);
            throw new DataAccessException(MSG_QUERY_FAILED);
        }
        return ($stmt->fetchColumn() > 0);
    }
}