<?php

namespace Lightuna\Service;

use Lightuna\Object\Board;

interface BoardServiceInterface
{
    public function createBoard(Board $board): void;

    public function getBoards(): array;

    public function getBoardByName(string $name): Board;

    public function GetBoardById(string $boardId): Board;
}