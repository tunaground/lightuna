<?php
namespace Lightuna\Object;

/**
 * Class Ban
 * @package Lightuna\Object
 */
class Ban
{
    /** @var int */
    private $banUid;
    /** @var int */
    private $threadUid;
    /** @var string */
    private $userId;
    /** @var string */
    private $ip;
    /** @var \DateTime */
    private $issueDate;

    /**
     * @param int $banUid
     * @param int $threadUid
     * @param string $userId
     * @param string $ip
     * @param \DateTime $issueDate
     */
    public function __construct(
        int $banUid,
        int $threadUid,
        string $userId,
        string $ip,
        \DateTime $issueDate
    ) {
        $this->banUid = $banUid;
        $this->threadUid = $threadUid;
        $this->userId = $userId;
        $this->ip = $ip;
        $this->issueDate = $issueDate;
    }

    /**
     * @return int
     */
    public function getBanUid(): int
    {
        return $this->banUid;
    }

    /**
     * @param int $banUid
     */
    public function setBanUid(int $banUid): void
    {
        $this->banUid = $banUid;
    }

    /**
     * @return int
     */
    public function getThreadUid(): int
    {
        return $this->threadUid;
    }

    /**
     * @param int $threadUid
     */
    public function setThreadUid(int $threadUid): void
    {
        $this->threadUid = $threadUid;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return \DateTime
     */
    public function getIssueDate(): \DateTime
    {
        return $this->issueDate;
    }

    /**
     * @param \DateTime $issueDate
     */
    public function setIssueDate(\DateTime $issueDate): void
    {
        $this->issueDate = $issueDate;
    }

}