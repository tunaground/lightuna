<?php

namespace Lightuna\Dao;

use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Object\Response;

interface ResponseDaoInterface
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
    public function getReponsesByThreadId(int $threadId): array;

    /**
     * @param int $threadId
     * @return int
     * @throws QueryException
     * @throws ResourceNotFoundException
     */
    public function getResponsesCountByThreadId(int $threadId): int;
}