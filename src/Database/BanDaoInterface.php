<?php
namespace Lightuna\Database;

use Lightuna\Object\Ban;

interface BanDaoInterface
{
    public function createBan(Ban $ban): void;

    public function getNextBanUid(): int;

    public function checkBanStatus(int $threadUid, string $userId, \DateTime $dateTime): bool;
}