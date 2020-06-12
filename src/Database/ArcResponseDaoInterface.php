<?php
namespace Lightuna\Database;

use Lightuna\Exception\DataAccessException;
use Lightuna\Object\ArcResponse;

/**
 * Interface ArcResponseInterface
 * @package Lightuna\Database
 */
interface ArcResponseDaoInterface
{
    /**
     * @param ArcResponse $arcResponse
     * @throws DataAccessException
     */
    public function createArcResponse(ArcResponse $arcResponse): void;

    /**
     * @return int
     * @throws DataAccessException
     */
    public function getNextArcResponseUid(): int;

    /**
     * @param int $responseUid
     * @return ArcResponse
     * @throws DataAccessException
     */
    public function getArcResponseByResponseUid(int $responseUid): ArcResponse;

    /**
     * @param int $arcResponseUid
     * @return mixed
     * @throws DataAccessException
     */
    public function deleteArcResponse(int $arcResponseUid);
}
