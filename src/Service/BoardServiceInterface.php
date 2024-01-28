<?php

namespace Lightuna\Service;

use Lightuna\Object\Board;
use Lightuna\Object\Notice;

interface BoardServiceInterface
{
    public function createBoard(Board $board, Notice $notice): void;

    public function updateBoard(Board $board): void;

    public function getBoards(): array;

    public function getBoardByName(string $name): Board;

    public function getBoardById(string $boardId): Board;

    public function getNoticeByBoardId(string $boardId): Notice;


    public function getNoticeByNoticeId(int $id): Notice;

    public function updateNotice(Notice $notice): void;
}