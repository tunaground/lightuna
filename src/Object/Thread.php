<?php
namespace Lightuna\Object;

use DateTime;

/**
 * Class Thread
 * @package Lightuna\Object
 */
class Thread
{
    /** @var string  */
    private $boardUid;
    /** @var int  */
    private $threadUid;
    /** @var string  */
    private $title;
    /** @var string  */
    private $password;
    /** @var string  */
    private $userName;
    /** @var DateTime  */
    private $createDate;
    /** @var DateTime  */
    private $updateDate;
    /** @var int */
    private $size;
    /** @var Response[] */
    private $responses;
    /** @var int */
    private $sequence;
    /** @var bool */
    private $dead;

    /**
     * Thread constructor.
     * @param string $boardUid
     * @param int $threadUid
     * @param string $title
     * @param string $password
     * @param string $userName
     * @param DateTime $createDate
     * @param DateTime $updateDate
     */
    public function __construct(
        string $boardUid,
        int $threadUid,
        string $title,
        string $password,
        string $userName,
        DateTime $createDate,
        DateTime $updateDate
    ) {
        $this->boardUid = $boardUid;
        $this->threadUid = $threadUid;
        $this->title = $title;
        $this->password = $password;
        $this->userName = $userName;
        $this->createDate = $createDate;
        $this->updateDate = $updateDate;
    }

    /**
     * @return string
     */
    public function getBoardUid(): string
    {
        return $this->boardUid;
    }

    /**
     * @return int
     */
    public function getThreadUid(): int
    {
        return $this->threadUid;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return DateTime
     */
    public function getCreateDate(): DateTime
    {
        return $this->createDate;
    }

    /**
     * @return DateTime
     */
    public function getUpdateDate(): DateTime
    {
        return $this->updateDate;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return Response[]
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @return bool
     */
    public function getDead(): bool
    {
        return $this->dead;
    }

    /**
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size)
    {
        $this->size = $size;
    }

    /**
     * @param Response[] $responses
     */
    public function setResponses(array $responses)
    {
        $this->responses = $responses;
    }

    /**
     * @param int $sequence
     */
    public function setSequence(int $sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * @param bool $dead
     */
    public function setDead(bool $dead)
    {
        $this->dead = $dead;
    }
}
