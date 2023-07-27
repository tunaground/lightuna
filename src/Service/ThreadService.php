<?php

namespace Lightuna\Service;

use Lightuna\Dao\MariadbResponseDao;
use Lightuna\Dao\MariadbThreadDao;
use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\Board;
use Lightuna\Object\Response;
use Lightuna\Object\Thread;
use Lightuna\Util\IdGenerator;

class ThreadService
{
    private MariadbThreadDao $threadDao;
    private MariadbResponseDao $responseDao;

    public function __construct(MariadbThreadDao $threadDao, MariadbResponseDao $responseDao)
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
    public function createThread(\PDO $pdo, Thread $thread, Response $response): void
    {
        $idGenerator = new IdGenerator();
        try {
            $pdo->beginTransaction();
            $this->threadDao->createThread($thread);
            $response->setSequence(0);
            $response->setUserId(
                $idGenerator->gen(str_replace('.', '0', $response->getIp())
                    . $response->getCreatedAt()->format('Ymd'))
            );
            $this->responseDao->createResponse($response);
            $pdo->commit();
        } catch (QueryException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function createResponse(Response $response): void
    {
        $idGenerator = new IdGenerator();
        $response->setSequence($this->responseDao->getResponsesCountByThreadId($response->getThreadId()));
        $response->setUserId(
            $idGenerator->gen(str_replace('.', '0', $response->getIp())
                . $response->getCreatedAt()->format('Ymd'))
        );
        $this->responseDao->createResponse($response);
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
    public function getResponses(int $threadId): array
    {
        return $this->responseDao->getReponsesByThreadId($threadId);
    }
}
