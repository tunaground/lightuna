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

    /**
     * @throws QueryException
     */
    public function createThread(\PDO $pdo, Thread $thread, Response $response): int
    {
        try {
            $pdo->beginTransaction();
            $threadId = $this->threadDao->getNextThreadId();
            $thread->setThreadId($threadId);
            $this->threadDao->createThread($thread);
            $response->setResponseId($this->responseDao->getNextResponseId());
            $response->setThreadId($threadId);
            $response->setSequence(0);
            // TODO:
            $response->setUserId('TESTER');
            $this->responseDao->createResponse($response);
            $pdo->commit();
            return $threadId;
        } catch (QueryException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function createReponse(Response $response): int
    {
        $idGenerator = new IdGenerator();
        $responseId = $this->responseDao->getNextResponseId();
        $response->setResponseId($responseId);
        $response->setSequence($this->responseDao->getResponsesCountByThreadId($response->getThreadId()));
        // TODO:
        $response->setUserId(
            $idGenerator->gen(str_replace('.', '0', $response->getIp())
                . $response->getCreatedAt()->format('Ymd'))
        );
        $this->responseDao->createResponse($response);
        return $responseId;
    }

    /**
     * @return Thread[]
     * @throws QueryException
     */
    public function getThreadsByBoardId(int $boardId, int $limit, int $offset = 0): array
    {
        return $this->threadDao->getThreadByBoardId($boardId, $limit, $offset);
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
