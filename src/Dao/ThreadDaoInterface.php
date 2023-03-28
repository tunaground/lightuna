<?php

namespace Lightuna\Dao;

use Lightuna\Exception\QueryException;
use Lightuna\Object\Thread;

interface ThreadDaoInterface
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
    public function getThreadByBoardId(int $boardId, int $limit, int $offset = 0): array;
}