<?php

namespace Lightuna\Object;

class Notice
{
    public function __construct(
        private ?int    $id = null,
        private ?string $boardId = null,
        private ?string $content = null,
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getBoardId(): ?string
    {
        return $this->boardId;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string|null $boardId
     */
    public function setBoardId(?string $boardId): void
    {
        $this->boardId = $boardId;
    }

    /**
     * @param string|null $content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }
}