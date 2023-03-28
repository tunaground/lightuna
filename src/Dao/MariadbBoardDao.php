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
    public function getNextBoardId(): int
    {
        $sql = <<<SQL
select nextval(seq_board_id);
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
    public function createBoard(Board $board)
    {
        $sql = <<<SQL
insert into board (board_id, name, deleted, created_at, updated_at, thread_limit)
values (:board_id, :name, :deleted, :created_at, :updated_at, :thread_limit)
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':board_id', $board->getBoardId());
        $stmt->bindValue(':name', $board->getName());
        $stmt->bindValue(':deleted', $board->isDeleted(), \PDO::PARAM_BOOL);
        $stmt->bindValue(':created_at', $board->getCreatedAt()->format(DATETIME_FORMAT));
        $stmt->bindValue(':updated_at', $board->getUpdatedAt()->format(DATETIME_FORMAT));
        $stmt->bindValue(':thread_limit', $board->getThreadLimit());
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
select board_id, name, deleted, created_at, updated_at, deleted_at, thread_limit from board;
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
select board_id, name, deleted, created_at, updated_at, deleted_at, thread_limit from board where name = :name; 
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
    public function getBoardById(int $boardId): Board
    {
        $sql = <<<SQL
select board_id, name, deleted, created_at, updated_at, deleted_at, thread_limit
from board
where board_id = :board_id; 
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':board_id', $boardId);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] !== '00000') {
            throw new QueryException($error[1]);
        }
        if ($stmt->rowCount() === 0) {
            throw new ResourceNotFoundException("board($boardId) not exists");
        }
        return $this->makeObject($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    private function makeObject(array $result): Board
    {
        return new Board(
            $result['board_id'],
            $result['name'],
            $result['deleted'],
            \DateTime::createFromFormat(DATETIME_FORMAT, $result['created_at']),
            \DateTime::createFromFormat(DATETIME_FORMAT, $result['updated_at']),
            ($result['deleted_at'] === null) ? null : \DateTime::createFromFormat(DATETIME_FORMAT, $result['deleted_at']),
            $result['thread_limit'],
        );
    }
}

