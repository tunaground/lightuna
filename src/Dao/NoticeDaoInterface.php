<?php

namespace Lightuna\Dao;

use Lightuna\Object\Notice;

interface NoticeDaoInterface
{
    public function getNextNoticeId(): int;

    public function getNotice(string $boardId): Notice;

    public function createNotice(Notice $notice): void;

    public function updateNotice(Notice $notice): void;
}