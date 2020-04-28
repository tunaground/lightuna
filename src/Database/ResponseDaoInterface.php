<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;
use Lightuna\Object\Response;

/**
 * Interface ResponseDaoInterface
 * @package Lightuna\Database
 */
interface ResponseDaoInterface
{
    /**
     * @param int $threadUid
     * @param int $start
     * @param int $limit
     * @return Response[]
     * @throws DataAccessException
     */
    public function getResponseListByThreadUid(int $threadUid, int $start, int $limit): array;

    /**
     * @param int $responseUid
     * @return Response
     * @throws DataAccessException
     */
    public function getResponseByResponseUid(int $responseUid): Response;

    /**
     * @param Response $response
     * @throws DataAccessException
     */
    public function createResponse(Response $response);

    /**
     * @param int $responseUid
     * @throws DataAccessException
     */
    public function deleteResponse(int $responseUid);

    /**
     * @return int
     * @throws DataAccessException
     */
    public function getNextResponseUid(): int;
}
