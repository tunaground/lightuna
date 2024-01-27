<?php

namespace Lightuna\Dao;

use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\Board;

interface BoardDaoInterface extends DaoInterface
{
    /**
     * @throws QueryException
     */
    public function createBoard(Board $board);

    /**
     * @return Board[]
     * @throws QueryException
     */
    public function getBoards(): array;

    /**
     * @throws QueryException
     * @throws ResourceNotFoundException
     */
    public function getBoardByName(string $name): Board;

    /**
     * @throws QueryException
     * @throws ResourceNotFoundException
     */
    public function getBoardById(string $id): Board;
}