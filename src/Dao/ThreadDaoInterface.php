<?php

namespace Lightuna\Dao;

use Lightuna\Exception\QueryException;
use Lightuna\Object\Thread;

interface ThreadDaoInterface extends DaoInterface
{
    /**
     * @throws QueryException
     */
    public function getNextThreadId(): int;

    /**
     * @throws QueryException
     */
    public function createThread(Thread $thread);

    /**
     * @return Thread[]
     * @throws QueryException
     */
    public function getThreadByBoardId(string $boardId, int $limit = 0, int $offset = 0): array;

    public function getThreadById(int $id): Thread;

    public function updateThread(Thread $thread): void;
}