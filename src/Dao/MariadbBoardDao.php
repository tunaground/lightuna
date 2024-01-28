<?php

namespace Lightuna\Dao;

use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\Board;

class MariadbBoardDao extends AbstractDao implements BoardDaoInterface
{
    /**
     * @throws QueryException
     */
    public function createBoard(Board $board)
    {
        $sql = <<<SQL
insert into board (id, name, created_at, updated_at)
values (:id, :name, :created_at, :updated_at)
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $board->getId());
        $stmt->bindValue(':name', $board->getName());
        $stmt->bindValue(':created_at', $board->getCreatedAt()->format(DATETIME_FORMAT));
        $stmt->bindValue(':updated_at', $board->getUpdatedAt()->format(DATETIME_FORMAT));
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
    }

    public function updateBoard(Board $board): void
    {
        $sql = <<<SQL
update board
set
name = :name,
default_username = :default_username,
display_thread = :display_thread,
display_thread_list = :display_thread_list,
display_response = :display_response,
display_response_line = :display_response_line,
limit_title = :limit_title,
limit_name = :limit_name,
limit_content = :limit_content,
limit_response = :limit_response,
limit_attachment_type = :limit_attachment_type,
limit_attachment_size = :limit_attachment_size,
limit_attachment_name = :limit_attachment_name,
interval_response = :interval_response,
interval_duplicate_response = :interval_duplicate_response,
updated_at = :updated_at
where id = :id
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $board->getId());
        $stmt->bindValue(':name', $board->getName());
        $stmt->bindValue(':default_username', $board->getDefaultUsername());
        $stmt->bindValue(':display_thread', $board->getDisplayThread());
        $stmt->bindValue(':display_thread_list', $board->getDisplayThreadList());
        $stmt->bindValue(':display_response', $board->getDisplayResponse());
        $stmt->bindValue(':display_response_line', $board->getDisplayResponseLine());
        $stmt->bindValue(':limit_title', $board->getLimitTitle());
        $stmt->bindValue(':limit_name', $board->getLimitName());
        $stmt->bindValue(':limit_content', $board->getLimitContent());
        $stmt->bindValue(':limit_response', $board->getLimitResponse());
        $stmt->bindValue(':limit_attachment_type', $board->getLimitAttachmentType());
        $stmt->bindValue(':limit_attachment_size', $board->getLimitAttachmentSize());
        $stmt->bindValue(':limit_attachment_name', $board->getLimitAttachmentName());
        $stmt->bindValue(':interval_response', $board->getIntervalResponse());
        $stmt->bindValue(':interval_duplicate_response', $board->getIntervalDuplicateResponse());
        $stmt->bindValue(':updated_at', $board->getUpdatedAt()->format(DATETIME_FORMAT));
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
    }

    /**
     * @return Board[]
     * @throws QueryException
     */
    public function getBoards(): array
    {
        $sql = <<<SQL
select * from board;
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $boards = [];
        foreach ($results as $result) {
            $boards[] = $this->makeObject($result);
        }
        return $boards;
    }

    /**
     * @throws QueryException
     * @throws ResourceNotFoundException
     */
    public function getBoardByName(string $name): Board
    {
        $sql = <<<SQL
select * from board where name = :name; 
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        if ($stmt->rowCount() === 0) {
            throw new ResourceNotFoundException("board($name) not exists");
        }
        return $this->makeObject($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * @throws QueryException
     * @throws ResourceNotFoundException
     */
    public function getBoardById(string $id): Board
    {
        $sql = <<<SQL
select *
from board
where id = :id; 
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        if ($stmt->rowCount() === 0) {
            throw new ResourceNotFoundException("board($id) not exists");
        }
        return $this->makeObject($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    private function makeObject(array $result): Board
    {
        return new Board(
            $result['id'],
            $result['name'],
            \DateTime::createFromFormat(DATETIME_FORMAT, $result['created_at']),
            \DateTime::createFromFormat(DATETIME_FORMAT, $result['updated_at']),
            ($result['deleted_at'] === null) ? null : \DateTime::createFromFormat(DATETIME_FORMAT, $result['deleted_at']),
            $result['default_username'],
            $result['display_thread'],
            $result['display_thread_list'],
            $result['display_response'],
            $result['display_response_line'],
            $result['limit_title'],
            $result['limit_name'],
            $result['limit_content'],
            $result['limit_response'],
            $result['limit_attachment_type'],
            $result['limit_attachment_size'],
            $result['limit_attachment_name'],
            $result['interval_response'],
            $result['interval_duplicate_response'],
        );
    }
}

