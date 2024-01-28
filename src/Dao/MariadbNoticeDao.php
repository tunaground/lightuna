<?php

namespace Lightuna\Dao;

use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\Notice;

class MariadbNoticeDao extends AbstractDao implements NoticeDaoInterface
{
    public function getNextNoticeId(): int
    {
        $sql = <<<SQL
select nextval(seq_notice_id);
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        return $stmt->fetchColumn();
    }

    public function getNoticeByBoardId(string $boardId): Notice
    {
        $sql = <<<SQL
select id, board_id, content
from notice
where board_id = :board_id
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':board_id', $boardId);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        if ($stmt->rowCount() === 0) {
            throw new ResourceNotFoundException("notice for board($boardId) not exists");
        }
        return $this->makeObject($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function getNoticeByNoticeId(int $id): Notice
    {
        $sql = <<<SQL
select id, board_id, content
from notice
where id = :id
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        if ($stmt->rowCount() === 0) {
            throw new ResourceNotFoundException("notice($id) not exists");
        }
        return $this->makeObject($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function createNotice(Notice $notice): void
    {
        $sql = <<<SQL
insert into notice (id, board_id, content)
values (:id, :board_id, :content)
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $notice->getId());
        $stmt->bindValue(':board_id', $notice->getBoardId());
        $stmt->bindValue(':content', $notice->getContent());
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
    }

    public function updateNotice(Notice $notice): void
    {
        $sql = <<<SQL
update notice
set content = :content
where id = :id
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $notice->getId());
        $stmt->bindValue(':content', $notice->getContent());
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
    }

    private function makeObject(array $result): Notice
    {
        return new Notice(
            $result['id'],
            $result['board_id'],
            $result['content'],
        );
    }
}