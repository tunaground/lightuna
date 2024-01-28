<?php

namespace Lightuna\Object;

class Board
{
    public function __construct(
        private ?string    $id = null,
        private ?string    $name = null,
        private ?\DateTime $createdAt = null,
        private ?\DateTime $updatedAt = null,
        private ?\DateTime $deletedAt = null,
        private ?string    $defaultUsername = null,
        private ?int       $displayThread = null,
        private ?int       $displayThreadList = null,
        private ?int       $displayResponse = null,
        private ?int       $displayResponseLine = null,
        private ?int       $limitTitle = null,
        private ?int       $limitName = null,
        private ?int       $limitContent = null,
        private ?int       $limitResponse = null,
        private ?string    $limitAttachmentType = null,
        private ?int       $limitAttachmentSize = null,
        private ?int       $limitAttachmentName = null,
        private ?int       $intervalResponse = null,
        private ?int       $intervalDuplicateResponse = null,
    )
    {
    }

    /**
     * @return int|null
     */
    public function getDisplayThreadList(): ?int
    {
        return $this->displayThreadList;
    }

    /**
     * @param int|null $displayThreadList
     */
    public function setDisplayThreadList(?int $displayThreadList): void
    {
        $this->displayThreadList = $displayThreadList;
    }

    /**
     * @return int|null
     */
    public function getDisplayResponse(): ?int
    {
        return $this->displayResponse;
    }

    /**
     * @param int|null $displayResponse
     */
    public function setDisplayResponse(?int $displayResponse): void
    {
        $this->displayResponse = $displayResponse;
    }

    /**
     * @return int|null
     */
    public function getDisplayResponseLine(): ?int
    {
        return $this->displayResponseLine;
    }

    /**
     * @param int|null $displayResponseLine
     */
    public function setDisplayResponseLine(?int $displayResponseLine): void
    {
        $this->displayResponseLine = $displayResponseLine;
    }

    /**
     * @return int|null
     */
    public function getLimitTitle(): ?int
    {
        return $this->limitTitle;
    }

    /**
     * @param int|null $limitTitle
     */
    public function setLimitTitle(?int $limitTitle): void
    {
        $this->limitTitle = $limitTitle;
    }

    /**
     * @return int|null
     */
    public function getLimitName(): ?int
    {
        return $this->limitName;
    }

    /**
     * @param int|null $limitName
     */
    public function setLimitName(?int $limitName): void
    {
        $this->limitName = $limitName;
    }

    /**
     * @return int|null
     */
    public function getLimitContent(): ?int
    {
        return $this->limitContent;
    }

    /**
     * @param int|null $limitContent
     */
    public function setLimitContent(?int $limitContent): void
    {
        $this->limitContent = $limitContent;
    }

    /**
     * @return int|null
     */
    public function getLimitResponse(): ?int
    {
        return $this->limitResponse;
    }

    /**
     * @param int|null $limitResponse
     */
    public function setLimitResponse(?int $limitResponse): void
    {
        $this->limitResponse = $limitResponse;
    }

    /**
     * @return string|null
     */
    public function getLimitAttachmentType(): ?string
    {
        return $this->limitAttachmentType;
    }

    /**
     * @param string|null $limitAttachmentType
     */
    public function setLimitAttachmentType(?string $limitAttachmentType): void
    {
        $this->limitAttachmentType = $limitAttachmentType;
    }

    /**
     * @return int|null
     */
    public function getLimitAttachmentSize(): ?int
    {
        return $this->limitAttachmentSize;
    }

    /**
     * @param int|null $limitAttachmentSize
     */
    public function setLimitAttachmentSize(?int $limitAttachmentSize): void
    {
        $this->limitAttachmentSize = $limitAttachmentSize;
    }

    /**
     * @return int|null
     */
    public function getLimitAttachmentName(): ?int
    {
        return $this->limitAttachmentName;
    }

    /**
     * @param int|null $limitAttachmentName
     */
    public function setLimitAttachmentName(?int $limitAttachmentName): void
    {
        $this->limitAttachmentName = $limitAttachmentName;
    }

    /**
     * @return int|null
     */
    public function getIntervalResponse(): ?int
    {
        return $this->intervalResponse;
    }

    /**
     * @param int|null $intervalResponse
     */
    public function setIntervalResponse(?int $intervalResponse): void
    {
        $this->intervalResponse = $intervalResponse;
    }

    /**
     * @return int|null
     */
    public function getIntervalDuplicateResponse(): ?int
    {
        return $this->intervalDuplicateResponse;
    }

    /**
     * @param int|null $intervalDuplicateResponse
     */
    public function setIntervalDuplicateResponse(?int $intervalDuplicateResponse): void
    {
        $this->intervalDuplicateResponse = $intervalDuplicateResponse;
    }

    /**
     * @return int|null
     */
    public function getDisplayThread(): ?int
    {
        return $this->displayThread;
    }

    /**
     * @param int|null $displayThread
     */
    public function setDisplayThread(?int $displayThread): void
    {
        $this->displayThread = $displayThread;
    }

    public
    function getId(): string
    {
        return $this->id;
    }

    public
    function getName(): string
    {
        return $this->name;
    }

    public
    function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public
    function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public
    function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public
    function setId(string $id)
    {
        $this->id = $id;
    }

    public
    function setName(string $name)
    {
        $this->name = $name;
    }

    public
    function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public
    function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public
    function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return string|null
     */
    public function getDefaultUsername(): ?string
    {
        return $this->defaultUsername;
    }

    /**
     * @param string|null $defaultUsername
     */
    public function setDefaultUsername(?string $defaultUsername): void
    {
        $this->defaultUsername = $defaultUsername;
    }
}

