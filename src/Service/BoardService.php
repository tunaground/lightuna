<?php

namespace Lightuna\Service;

use Lightuna\Dao\BoardDaoInterface;
use Lightuna\Dao\NoticeDaoInterface;
use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\Board;
use Lightuna\Object\Notice;

class BoardService implements BoardServiceInterface
{
    private BoardDaoInterface $boardDao;
    private NoticeDaoInterface $noticeDao;

    public function __construct(
        BoardDaoInterface  $boardDao,
        NoticeDaoInterface $noticeDao
    )
    {
        $this->boardDao = $boardDao;
        $this->noticeDao = $noticeDao;
    }

    /**
     * @throws QueryException
     */
    public function createBoard(Board $board, Notice $notice): void
    {
        $this->boardDao->createBoard($board);
        $notice->setId($this->noticeDao->getNextNoticeId());
        $this->noticeDao->createNotice($notice);
    }

    public function updateBoard(Board $board): void
    {
        $this->boardDao->updateBoard($board);
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

    public function getNotice(Board $board): Notice
    {
        return $this->noticeDao->getNotice($board->getId());
    }

    public function updateNotice(Notice $notice): void
    {
        $this->noticeDao->updateNotice($notice);
    }
}

