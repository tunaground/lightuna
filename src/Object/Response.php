<?php
namespace Lightuna\Object;

use DateTime;

/**
 * Class Response
 * @package Lightuna\Object
 */
class Response
{
    /** @var int  */
    private $threadUid;
    /** @var int  */
    private $responseUid;
    /** @var int  */
    private $sequence;
    /** @var string  */
    private $userName;
    /** @var string  */
    private $userId;
    /** @var string  */
    private $ip;
    /** @var DateTime  */
    private $createDate;
    /** @var ResponseContent  */
    private $content;
    /** @var string  */
    private $attachment;
    /** @var array */
    private $attachmentInfo;

    /**
     * Response constructor.
     * @param int $threadUid
     * @param int $responseUid
     * @param int $sequence
     * @param string $userName
     * @param string $userId
     * @param string $ip
     * @param DateTime $createDate
     * @param ResponseContent $content
     * @param string $attachment
     */
    public function __construct(
        int $threadUid,
        int $responseUid,
        int $sequence,
        string $userName,
        string $userId,
        string $ip,
        DateTime $createDate,
        ResponseContent $content,
        string $attachment
    ) {
        $this->threadUid = $threadUid;
        $this->responseUid = $responseUid;
        $this->sequence = $sequence;
        $this->userName = $userName;
        $this->userId = $userId;
        $this->ip = $ip;
        $this->createDate = $createDate;
        $this->content = $content;
        $this->attachment = $attachment;
    }

    /**
     * @return int
     */
    public function getThreadUid(): int
    {
        return $this->threadUid;
    }

    /**
     * @return int
     */
    public function getResponseUid(): int
    {
        return $this->responseUid;
    }

    /**
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return DateTime
     */
    public function getCreateDate(): DateTime
    {
        return $this->createDate;
    }

    /**
     * @return ResponseContent
     */
    public function getContent(): ResponseContent
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getAttachment(): string
    {
        return $this->attachment;
    }

    /**
     * @return array
     */
    public function getAttachmentInfo(): array
    {
        return $this->attachmentInfo;
    }

    /**
     * @param array $attachmentInfo
     */
    public function setAttachmentInfo(array $attachmentInfo)
    {
        $this->attachmentInfo = $attachmentInfo;
    }
}
