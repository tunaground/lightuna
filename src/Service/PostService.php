<?php
namespace Lightuna\Service;

use DateTime;
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
    /** @var Board */
    private $board;

    /**
     * PostService constructor.
     * @param DataSource $dataSource
     * @param ThreadDaoInterface $threadDao
     * @param ResponseDaoInterface $responseDao
     * @param Board $board
     */
    public function __construct(
        DataSource $dataSource,
        ThreadDaoInterface $threadDao,
        ResponseDaoInterface $responseDao,
        Board $board
    ) {
        $this->dataSource = $dataSource;
        $this->threadDao = $threadDao;
        $this->responseDao = $responseDao;
        $this->board = $board;
    }

    /**
     * @param string $userName
     * @param array $console
     * @param string $content
     * @param string $attachment
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
        string $title,
        string $password,
        string $ip,
        DateTime $currentDateTime
    ) {
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
                $currentDateTime
            );
            $titleLength = mb_strlen($thread->getTitle());
            if ($titleLength < 1 || $titleLength > $this->board['maxTitleLength']) {
                throw new InvalidUserInputException('Title length too long.');
            }
            $this->threadDao->createThread($thread);
            $this->postResponse(
                $threadUid,
                $userName,
                $console,
                $content,
                $attachment,
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
        string $ip,
        DateTime $currentDateTime
    ) {
        if (array_key_exists('lastResponseDateTime', $_SESSION) !== false) {
            $lastResponseDateTime = DateTime::createFromFormat(
                PostService::DATETIME_FORMAT,
                $_SESSION['lastResponseDateTime']
            );
            $responseInterval = $currentDateTime->getTimestamp() - $lastResponseDateTime->getTimestamp();
            if ($responseInterval < $this->board['maxResponseInterval']) {
                throw new InvalidUserInputException('Too many response.');
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
            throw new InvalidUserInputException('Too many duplicated response content.');
        } else {
            $_SESSION['lastContentHash'] = $currentResponseContentHash;
        }
        $userName = preg_replace_callback("/([^\#]*)\#(.+)/", function ($matches) {
            return $matches[1] . '<b>◆' . mb_substr(crypt($matches[2]), -10) . '</b>';
        }, $userName);
        if (mb_strlen($userName) > $this->board['maxNameLength']) {
            throw new InvalidUserInputException('User name too long.');
        }

        $isResponseTransaction = false;
        try {
            if (!$this->dataSource->isTransaction()) {
                $this->dataSource->beginTransaction();
                $isResponseTransaction = true;
            }
            $thread = $this->threadDao->getThreadByThreadUid($threadUid);
            $responseSequence = $this->threadDao->getLastResponseSequence($threadUid) + 1;
            $responseId = $this->responseDao->getNextResponseUid();
            $userId = mb_substr(crypt($ip, $currentDateTime->format('Ymd')), -10);
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
            $response = new Response(
                $thread->getThreadUid(),
                $responseId,
                $responseSequence,
                $userName,
                $userId,
                $ip,
                $currentDateTime,
                $responseContent,
                $attachment
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
}
