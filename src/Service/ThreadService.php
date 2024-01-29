<?php

namespace Lightuna\Service;

use Lightuna\Dao\ResponseDaoInterface;
use Lightuna\Dao\ThreadDaoInterface;
use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\PostOption;
use Lightuna\Object\Response;
use Lightuna\Object\Thread;
use Lightuna\Util\IdGenerator;
use Lightuna\Util\ContentUtil;

class ThreadService implements ThreadServiceInterface
{
    private ThreadDaoInterface $threadDao;
    private ResponseDaoInterface $responseDao;

    public function __construct(ThreadDaoInterface $threadDao, ResponseDaoInterface $responseDao)
    {
        $this->threadDao = $threadDao;
        $this->responseDao = $responseDao;
    }

    public function getNextThreadId(): int
    {
        return $this->threadDao->getNextThreadId();
    }

    public function getNextResponseId(): int
    {
        return $this->responseDao->getNextResponseId();
    }

    /**
     * @throws QueryException
     */
    public function createThread(Thread $thread, Response $response, PostOption $postOption): void
    {
        try {
            $idGenerator = new IdGenerator();
            $pdo = $this->threadDao->getPdo();
            $pdo->beginTransaction();
            $this->threadDao->createThread($thread);
            $response->setSequence(0);
            $response->setUserId(
                $idGenerator->gen(str_replace('.', '0', $response->getIp())
                    . $response->getCreatedAt()->format('Ymd'))
            );
            $content = ContentUtil::newLineToBreak($response->getContent());
            if ($postOption->isRich()) {
                $content = ContentUtil::applyRichContent($content);
            }
            if ($postOption->isAa()) {
                $content = ContentUtil::applyAsciiArtTagAll($content);
            }
            $response->setContent($content);
            $this->responseDao->setPdo($pdo);
            $this->responseDao->createResponse($response);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function createResponse(Response $response, PostOption $postOption): void
    {
        $idGenerator = new IdGenerator();
        $content = ContentUtil::newLineToBreak($response->getContent());
        if ($postOption->isRich()) {
            $content = ContentUtil::applyRichContent($content);
        }
        if ($postOption->isAa()) {
            $content = ContentUtil::applyAsciiArtTagAll($content);
        }

        $response->setContent($content);
        $response->setSequence($this->responseDao->getResponsesCountByThreadId($response->getThreadId()));
        $response->setUserId(
            $idGenerator->gen(str_replace('.', '0', $response->getIp())
                . $response->getCreatedAt()->format('Ymd'))
        );

        if (!$postOption->isNoup()) {
            $pdo = $this->responseDao->getPdo();
            $this->threadDao->setPdo($pdo);
            $thread = $this->threadDao->getThreadById($response->getThreadId());
            $thread->setUpdatedAt($response->getCreatedAt());
            try {
                $pdo->beginTransaction();
                $this->responseDao->createResponse($response);
                $this->threadDao->updateThread($thread);
                $pdo->commit();
            } catch (\Throwable $e) {
                $pdo->rollBack();
                throw $e;
            }
        } else {
            $this->responseDao->createResponse($response);
        }
    }

    /**
     * @return Thread[]
     * @throws QueryException
     */
    public function getThreadsByBoardId(string $boardId, int $limit = 0, int $offset = 0): array
    {
        return $this->threadDao->getThreadByBoardId($boardId, $limit, $offset);
    }

    public function getThreadById(int $id): Thread
    {
        return $this->threadDao->getThreadById($id);
    }

    /**
     * @param int $threadId
     * @return array
     * @throws QueryException
     * @throws ResourceNotFoundException
     */
    public function getResponsesByThreadId(int $threadId, int $limit = 0, int $offset = 0): array
    {
        return $this->responseDao->getResponsesByThreadId($threadId, $limit, $offset);
    }

    public function getResponseCountByThreadId(int $threadId): int
    {
        return $this->responseDao->getResponsesCountByThreadId($threadId);
    }

    /**
     * @param int $id
     * @return Response
     * @throws InvalidUserInputException
     */
    public function deleteResponseById(int $id, string $password): Response
    {
        $response = $this->responseDao->getResponseById($id);
        if ($response->getSequence() === 0) {
            throw new InvalidUserInputException('invalid response sequence');
        }

        $thread = $this->getThreadById($response->getThreadId());
        if ($thread->getPassword() !== $password) {
            throw new InvalidUserInputException('wrong password');
        }

        $response->setDeletedAt(new \DateTime());
        $this->responseDao->updateResponse($response);

        return $response;
    }

    /**
     * @param int $id
     * @return Response
     * @throws InvalidUserInputException
     */
    public function restoreResponseId(int $id, string $password): Response
    {
        $response = $this->responseDao->getResponseById($id);
        if ($response->getSequence() === 0) {
            throw new InvalidUserInputException('invalid response sequence');
        }

        $thread = $this->getThreadById($response->getThreadId());
        if ($thread->getPassword() !== $password) {
            throw new InvalidUserInputException('wrong password');
        }

        $response->setDeletedAt(null);
        $this->responseDao->updateResponse($response);

        return $response;
    }

    public function getResponseById(int $id): Response
    {
        return $this->responseDao->getResponseById($id);
    }
}
