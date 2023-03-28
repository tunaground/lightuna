<?php

namespace Lightuna\Object;

class Board
{
    public function __construct(
        private ?int       $boardId = null,
        private ?string    $name = null,
        private ?bool      $deleted = null,
        private ?\DateTime $createdAt = null,
        private ?\DateTime $updatedAt = null,
        private ?\DateTime $deletedAt = null,
        private ?int       $threadLimit = null,
    )
    {
    }

    public function getBoardId(): int
    {
        return $this->boardId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function getThreadLimit(): int
    {
        return $this->threadLimit;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setBoardId(int $boardId)
    {
        $this->boardId = $boardId;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setDeleted(bool $deleted)
    {
        $this->deleted = $deleted;
    }

    public function setThreadLimit(int $threadLimit)
    {
        $this->threadLimit = $threadLimit;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
}

