<?php

namespace Lightuna\Dao;

use Lightuna\Object\Notice;

interface NoticeDaoInterface extends DaoInterface
{
    public function getNextNoticeId(): int;

    public function getNoticeByBoardId(string $boardId): Notice;

    public function getNoticeByNoticeId(int $id): Notice;

    public function createNotice(Notice $notice): void;

    public function updateNotice(Notice $notice): void;
}