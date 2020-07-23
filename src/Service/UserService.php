<?php
namespace Lightuna\Service;

use Lightuna\Database\BanDaoInterface;
use Lightuna\Database\ResponseDaoInterface;
use Lightuna\Exception\DataAccessException;
use Lightuna\Object\Ban;

class UserService
{
    /** @var ResponseDaoInterface */
    private $responseDao;
    /** @var BanDaoInterface */
    private $banDao;

    /**
     * @param ResponseDaoInterface $responseDao
     * @param BanDaoInterface $banDao
     */
    public function __construct(ResponseDaoInterface $responseDao, BanDaoInterface $banDao)
    {
        $this->responseDao = $responseDao;
        $this->banDao = $banDao;
    }

    /**
     * @param string $responseUid
     * @throws DataAccessException
     */
    public function banUserId(string $responseUid)
    {
        try {
            $response = $this->responseDao->getResponseByResponseUid($responseUid);
            $banUid = $this->banDao->getNextBanUid();
            $ban = new Ban(
                $banUid,
                $response->getThreadUid(),
                $response->getUserId(),
                $response->getIp(),
                new \DateTime()
            );
            $this->banDao->createBan($ban);
        } catch (DataAccessException $e) {
            throw $e;
        }
    }
}
