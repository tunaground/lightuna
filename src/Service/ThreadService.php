<?php
namespace Lightuna\Service;

use Lightuna\Database\ThreadDaoInterface;
use Lightuna\Exception\DataAccessException;
use Lightuna\Exception\InvalidUserInputException;

/**
 * Class ThreadService
 * @package Lightuna\Service
 */
class ThreadService
{
    /** @var ThreadDaoInterface */
    private $threadDao;

    public function __construct(ThreadDaoInterface $threadDao)
    {
        $this->threadDao = $threadDao;
    }

    /**
     * @param int $threadUid
     * @param string $password
     * @throws DataAccessException
     * @throws InvalidUserInputException
     */
    public function checkThreadPassword(int $threadUid, string $password)
    {
        try {
            $thread = $this->threadDao->getThreadByThreadUid($threadUid);
        } catch (DataAccessException $e) {
            throw $e;
        }
        if ($thread->getPassword() !== hash('sha256', $password)) {
            throw new InvalidUserInputException(MSG_INVALID_PASSWORD);
        }
    }

    public function endThread(int $threadUid)
    {
        try {
            $this->threadDao->setThreadEnd($threadUid, true);
        } catch (DataAccessException $e) {
            throw $e;
        }
    }
}
