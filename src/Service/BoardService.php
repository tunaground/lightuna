<?php

namespace Lightuna\Service;

use Lightuna\Dao\MariadbBoardDao;
use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\Board;

class BoardService
{
    private MariadbBoardDao $boardDao;

    public function __construct(MariadbBoardDao $boardDao)
    {
        $this->boardDao = $boardDao;
    }

    /**
     * @throws QueryException
     */
    public function createBoard(Board $board)
    {
        $board->setBoardId($this->boardDao->getNextBoardId());
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

    public function getBoardById(int $boardId): Board
    {
        return $this->boardDao->getBoardById($boardId);
    }
}

