<?php

namespace Lightuna\Dao;

use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\Response;

interface ResponseDaoInterface extends DaoInterface
{
    /**
     * @throws QueryException
     */
    public function getNextResponseId(): int;

    /**
     * @throws QueryException
     */
    public function createResponse(Response $response);

    /**
     * @param int $threadId
     * @return Response[]
     * @throws QueryException
     * @throws ResourceNotFoundException
     */
    public function getResponsesByThreadId(int $threadId, int $limit, int $offset): array;

    /**
     * @param int $threadId
     * @return int
     * @throws QueryException
     * @throws ResourceNotFoundException
     */
    public function getResponsesCountByThreadId(int $threadId): int;

    public function getResponseById(int $id): Response;

    public function updateResponse(Response $response): void;
}