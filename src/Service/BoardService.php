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
    public function createBoard(string $id, string $name): void
    {
        try {
            $dateTime = new \DateTime();
            $board = new Board();
            $board->setId($id);
            $board->setName($name);
            $board->setCreatedAt($dateTime);
            $board->setUpdatedAt($dateTime);

            $notice = new Notice();
            $notice->setBoardId($board->getId());
            $notice->setContent("");

            $pdo = $this->boardDao->getPdo();
            $this->noticeDao->setPdo($pdo);

            $pdo->beginTransaction();
            $this->boardDao->createBoard($board);
            $notice->setId($this->noticeDao->getNextNoticeId());
            $this->noticeDao->createNotice($notice);
            $pdo->commit();
        } catch (QueryException $e) {
            $pdo->rollBack();
            throw $e;
        }
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

    public function getNoticeByBoardId(string $boardId): Notice
    {
        return $this->noticeDao->getNoticeByBoardId($boardId);
    }

    public function getNoticeByNoticeId(int $id): Notice
    {
        return $this->noticeDao->getNoticeByNoticeId($id);
    }

    public function updateNotice(Notice $notice): void
    {
        $this->noticeDao->updateNotice($notice);
    }
}

