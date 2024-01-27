<?php

namespace Lightuna\Service;

use Lightuna\Dao\BoardDaoInterface;
use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\Board;

class BoardService implements BoardServiceInterface
{
    private BoardDaoInterface $boardDao;

    public function __construct(BoardDaoInterface $boardDao)
    {
        $this->boardDao = $boardDao;
    }

    /**
     * @throws QueryException
     */
    public function createBoard(Board $board): void
    {
        $this->boardDao->createBoard($board);
    }

    /**
     * @return Board[]
     * @throws QueryException
     */
    public function getBoards(): array
    {
        return $this->boardDao->getBoards();
    }

    /**
     * @throws QueryException
     * @throws ResourceNotFoundException
     */
    public function getBoardByName(string $name): Board
    {
        return $this->boardDao->getBoardByName($name);
    }

    public function getBoardById(string $boardId): Board
    {
        return $this->boardDao->getBoardById($boardId);
    }
}

