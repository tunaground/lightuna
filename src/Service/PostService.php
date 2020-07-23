<?php
namespace Lightuna\Service;

use DateTime;
use Lightuna\Database\BanDaoInterface;
use Lightuna\Database\DataSource;
use Lightuna\Database\ResponseDaoInterface;
use Lightuna\Database\ThreadDaoInterface;
use Lightuna\Exception\DataAccessException;
use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Object\Board;
use Lightuna\Object\Response;
use Lightuna\Object\ResponseContent;
use Lightuna\Object\Thread;

/**
 * Class PostService
 * @package Lightuna\Service
 */
class PostService
{
    /** @var string */
    const DATETIME_FORMAT = 'Y-m-d\TH:i:s.u';
    /** @var string */
    const HASH_SHA256 = 'sha256';
    /** @var DataSource */
    private $dataSource;
    /** @var ThreadDaoInterface */
    private $threadDao;
    /** @var ResponseDaoInterface */
    private $responseDao;
    /** @var BanDaoInterface */
    private $banDao;
    /** @var Board */
    private $board;

    /**
     * PostService constructor.
     * @param DataSource $dataSource
     * @param ThreadDaoInterface $threadDao
     * @param ResponseDaoInterface $responseDao
     * @param BanDaoInterface $banDao
     * @param Board $board
     */
    public function __construct(
        DataSource $dataSource,
        ThreadDaoInterface $threadDao,
        ResponseDaoInterface $responseDao,
        BanDaoInterface $banDao,
        Board $board
    ) {
        $this->dataSource = $dataSource;
        $this->threadDao = $threadDao;
        $this->responseDao = $responseDao;
        $this->banDao = $banDao;
        $this->board = $board;
    }

    /**
     * @param string $userName
     * @param array $console
     * @param string $content
     * @param string $attachment
     * @param string $youtube
     * @param string $title
     * @param string $password
     * @param string $ip
     * @param DateTime $currentDateTime
     * @throws \PDOException
     * @throws InvalidUserInputException
     */
    public function postThread(
        string $userName,
        array $console,
        string $content,
        string $attachment,
        string $youtube,
        string $title,
        string $password,
        string $ip,
        DateTime $currentDateTime
    ) {
        if ($password === '') {
            $password = rand();
        }
        try {
            $userName = $this->makeUserName($userName);
        } catch (InvalidUserInputException $e) {
            throw $e;
        }
        try {
            $this->dataSource->beginTransaction();
            $threadUid = $this->threadDao->getNextThreadUid();
            $thread = new Thread(
                $this->board['uid'],
                $threadUid,
                $title,
                hash(self::HASH_SHA256, $password),
                $userName,
                $currentDateTime,
                $currentDateTime,
                false
            );
            $titleLength = mb_strlen($thread->getTitle());
            if ($titleLength < 1 || $titleLength > $this->board['maxTitleLength']) {
                throw new InvalidUserInputException(MSG_LIMIT_TITLE_LENGTH);
            }
            $this->threadDao->createThread($thread);
            $this->postResponse(
                $threadUid,
                $userName,
                $console,
                $content,
                $attachment,
                $youtube,
                $ip,
                $currentDateTime
            );
            $this->dataSource->commit();
        } catch (\PDOException $e) {
            $this->dataSource->rollBack();
            throw $e;
        } catch (DataAccessException $e) {
            $this->dataSource->rollBack();
            throw $e;
        } catch (InvalidUserInputException $e) {
            $this->dataSource->rollBack();
            throw $e;
        }
    }

    /**
     * @param int $threadUid
     * @param string $userName
     * @param array $console
     * @param string $content
     * @param string $attachment
     * @param string $youtube
     * @param string $ip
     * @param DateTime $currentDateTime
     * @throws InvalidUserInputException
     * @throws \PDOException
     */
    public function postResponse(
        int $threadUid,
        string $userName,
        array $console,
        string $content,
        string $attachment,
        string $youtube,
        string $ip,
        DateTime $currentDateTime
    ) {
        try {
            $userName = $this->makeUserName($userName);
            $userId = $this->makeUserId($ip, $currentDateTime);
            $responseContent = $this->makeResponseContent($content, $console);
            $this->checkAbuse($currentDateTime, $content);
        } catch (InvalidUserInputException $e) {
            throw $e;
        }

        if ($this->banDao->checkBanStatus($threadUid, $userId, $currentDateTime)) {
            throw new InvalidUserInputException(MSG_DENIED_USER);
        }

        $isResponseTransaction = false;
        try {
            if (!$this->dataSource->isTransaction()) {
                $this->dataSource->beginTransaction();
                $isResponseTransaction = true;
            }
            $thread = $this->threadDao->getThreadByThreadUid($threadUid);
            $responseSequence = $this->threadDao->getLastResponseSequence($threadUid) + 1;
            if ($responseSequence > $this->board['maxResponseSize']) {
                throw new InvalidUserInputException(MSG_THREAD_ENDED);
            }
            if ($responseSequence === $this->board['maxResponseSize']) {
                $this->threadDao->setThreadEnd($thread->getThreadUid(), true);
            }
            $responseId = $this->responseDao->getNextResponseUid();
            $response = new Response(
                $thread->getThreadUid(),
                $responseId,
                $responseSequence,
                $userName,
                $userId,
                $ip,
                $currentDateTime,
                $responseContent,
                $attachment,
                $youtube
            );
            $this->responseDao->createResponse($response);
            if (!in_array('noup', $console, true)) {
                $this->threadDao->setUpdateDate($thread->getThreadUid(), $currentDateTime);
            }
            if ($isResponseTransaction) {
                $this->dataSource->commit();
            }
        } catch (\PDOException $e) {
            if ($isResponseTransaction) {
                $this->dataSource->rollBack();
            }
            throw $e;
        } catch (DataAccessException $e) {
            if ($isResponseTransaction) {
                $this->dataSource->rollBack();
            }
            throw $e;
        }
    }

    public function testResponse(
        string $userName,
        array $console,
        string $content,
        string $ip,
        DateTime $currentDateTime
    ) {
        try {
            return [
                'userName' => $this->makeUserName($userName),
                'userId' => $this->makeUserId($ip, $currentDateTime),
                'content' => $this->makeResponseContent($content, $console)->__toString(),
                'createDate' => $currentDateTime->format('Y-m-d H:i:s')
            ];
        } catch (InvalidUserInputException $e) {
            throw $e;
        }

    }

    /**
     * @param DateTime $currentDateTime
     * @param string $content
     * @throws InvalidUserInputException
     */
    private function checkAbuse(DateTime $currentDateTime, string $content)
    {
        if (array_key_exists('lastResponseDateTime', $_SESSION) !== false) {
            $lastResponseDateTime = DateTime::createFromFormat(
                PostService::DATETIME_FORMAT,
                $_SESSION['lastResponseDateTime']
            );
            $responseInterval = $currentDateTime->getTimestamp() - $lastResponseDateTime->getTimestamp();
            if ($responseInterval < $this->board['maxResponseInterval']) {
                throw new InvalidUserInputException(MSG_TOO_MANY_RESPONSE);
            }
            if ($responseInterval > $this->board['maxDuplicateResponseInterval']) {
                unset($_SESSION['lastContentHash']);
            }
        }
        $_SESSION['lastResponseDateTime'] = $currentDateTime->format(PostService::DATETIME_FORMAT);

        $currentResponseContentHash = hash(self::HASH_SHA256, $content);
        if (
            array_key_exists('lastContentHash', $_SESSION)
            && $_SESSION['lastContentHash'] === $currentResponseContentHash
        ) {
            throw new InvalidUserInputException(MSG_TOO_MANY_DUPLICATED_RESPONSE);
        } else {
            $_SESSION['lastContentHash'] = $currentResponseContentHash;
        }
    }

    /**
     * @param string $userName
     * @throws InvalidUserInputException
     */
    private function makeUserName(string $userName)
    {
        $userName = preg_replace_callback("/([^\#]*)\#(.+)/", function ($matches) {
            return $matches[1] . '<b>◆' . mb_substr(crypt($matches[2]), -10) . '</b>';
        }, $userName);
        if (mb_strlen($userName) > $this->board['maxNameLength']) {
            throw new InvalidUserInputException(MSG_LIMIT_USER_NAME_LENGTH);
        }
        return $userName;
    }

    /**
     * @param string $ip
     * @param DateTime $currentDateTime
     */
    private function makeUserId(string $ip, Datetime $currentDateTime)
    {
        return mb_substr(crypt($currentDateTime->format('Ymd'), $ip), -10);
    }

    /**
     * @param string $content
     * @param array $console
     * @throws InvalidUserInputException
     */
    private function makeResponseContent(string $content, array $console)
    {
        $responseContent = new ResponseContent($content);
        $responseContent->newLineToBreak();
        if (in_array('off', $console, true) !== true) {
            $responseContent->applyAsciiArtTag();
            $responseContent->applyHorizonTag();
            $responseContent->applySpoilerTag();
            $responseContent->applyColorTag();
            $responseContent->applyRubyTag();
            $responseContent->applyDiceTag();
        }
        if (in_array('aa', $console, true) === true) {
            $responseContent->applyAsciiArtTagAll();
        }
        if (mb_strlen($responseContent) > $this->board['maxContentLength']) {
            throw new InvalidUserInputException(MSG_LIMIT_CONTENT_LENGTH);
        }
        return $responseContent;
    }


    private function embedYoutube(string $youtube): string
    {
        preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $youtube, $matches);
        return '<iframe class="youtube" src="https://www.youtube.com/embed/' . $matches[1] . '" frameborder="0" allowfullscreen></iframe>';
    }
}
