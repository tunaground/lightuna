<?php
namespace Lightuna\Service;

use Lightuna\Database\ResponseDaoInterface;
use Lightuna\Database\ThreadDaoInterface;
use Lightuna\Exception\DataAccessException;
use Lightuna\Object\Board;
use Lightuna\Object\Response;
use Lightuna\Object\Thread;

class SearchService
{
    /** @var Board */
    private $board;
    /** @var ThreadDaoInterface */
    private $threadDao;
    /** @var ResponseDaoInterface */
    private $responseDao;

    public function __construct(
        Board $board,
        ThreadDaoInterface $threadDao,
        ResponseDaoInterface $responseDao
    ) {
        $this->board = $board;
        $this->threadDao = $threadDao;
        $this->responseDao = $responseDao;
    }

    /**
     * @param string $keyword
     * @param int $start
     * @param int $limit
     * @return Thread[]
     * @throws DataAccessException
     */
    public function findByThreadTitle(string $keyword, int $start, int $limit): array
    {
        try {
            $threads = $this->threadDao->findByThreadTitle($this->board['uid'], $keyword, $start, $limit);
            for ($i = 0; $i < sizeof($threads); $i++) {
                $threads[$i]->setSize($this->threadDao->getLastResponseSequence($threads[$i]->getThreadUid()));
                $threads[$i]->setSequence($i + 1 + $start);
            }
            return $threads;
        } catch (DataAccessException $e) {
            throw $e;
        }
    }

    /**
     * @param string $keyword
     * @param int $start
     * @param int $limit
     * @return Thread[]
     * @throws DataAccessException
     */
    public function findByThreadOwner(string $keyword, int $start, int $limit): array
    {
        try {
            $threads = $this->threadDao->findByThreadOwner($this->board['uid'], $keyword, $start, $limit);
            for ($i = 0; $i < sizeof($threads); $i++) {
                $threads[$i]->setSize($this->threadDao->getLastResponseSequence($threads[$i]->getThreadUid()));
                $threads[$i]->setSequence($i + 1 + $start);
            }
            return $threads;
        } catch (DataAccessException $e) {
            throw $e;
        }
    }

    /**
     * @param string $keyword
     * @param int $start
     * @param int $limit
     * @return Response[]
     * @throws DataAccessException
     */
    public function findByResponseUserName(string $keyword, int $start, int $limit): array
    {
        try {
            return $this->responseDao->findByResponseUserName($keyword, $start, $limit);
        } catch (DataAccessException $e) {
            throw $e;
        }
    }

    /**
     * @param string $keyword
     * @param int $start
     * @param int $limit
     * @return Response[]
     * @throws DataAccessException
     */
    public function findByResponseUserId(string $keyword, int $start, int $limit): array
    {
        try {
            return $this->responseDao->findByResponseUserId($keyword, $start, $limit);
        } catch (DataAccessException $e) {
            throw $e;
        }
    }
}
