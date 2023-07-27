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
insert into board (id, name, deleted, created_at, updated_at)
values (:id, :name, :deleted, :created_at, :updated_at)
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $board->getId());
        $stmt->bindValue(':name', $board->getName());
        $stmt->bindValue(':deleted', $board->isDeleted(), \PDO::PARAM_BOOL);
        $stmt->bindValue(':created_at', $board->getCreatedAt()->format(DATETIME_FORMAT));
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
            $result['deleted'],
            \DateTime::createFromFormat(DATETIME_FORMAT, $result['created_at']),
            \DateTime::createFromFormat(DATETIME_FORMAT, $result['updated_at']),
            ($result['deleted_at'] === null) ? null : \DateTime::createFromFormat(DATETIME_FORMAT, $result['deleted_at']),
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

