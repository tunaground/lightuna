<?php
namespace Lightuna\Service;

use Lightuna\Database\ArcResponseDao;
use Lightuna\Database\DataSource;
use Lightuna\Database\ResponseDao;
use Lightuna\Database\ThreadDao;
use Lightuna\Exception\DataAccessException;
use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Object\ArcResponse;

/**
 * Class ResponseService
 * @package Lightuna\Service
 */
class ResponseService
{
    /** @var DataSource */
    private $dataSource;
    /** @var ThreadDao */
    private $threadDao;
    /** @var ResponseDao */
    private $responseDao;
    /** @var ArcResponseDao */
    private $arcResponseDao;

    /**
     * ResponseService constructor.
     * @param DataSource $dataSource
     * @param ThreadDao $threadDao
     * @param ResponseDao $responseDao
     * @param ArcResponseDao $arcResponseDao
     */
    public function __construct(
        DataSource $dataSource,
        ThreadDao $threadDao,
        ResponseDao $responseDao,
        ArcResponseDao $arcResponseDao
    ) {
        $this->dataSource = $dataSource;
        $this->threadDao = $threadDao;
        $this->responseDao = $responseDao;
        $this->arcResponseDao = $arcResponseDao;
    }

    /**
     * @param int $threadUid
     * @param int $responseUid
     * @param string $threadPassword
     * @throws \PDOException
     * @throws InvalidUserInputException
     * @throws DataAccessException
     */
    public function archiveResponse(int $threadUid, int $responseUid, string $threadPassword)
    {
        try {
            $this->dataSource->beginTransaction();
            $arcResponseUid = $this->arcResponseDao->getNextArcResponseUid();
            $response = $this->responseDao->getResponseByResponseUid($responseUid);
            if ((int)$response->getThreadUid() !== $threadUid) {
                throw new InvalidUserInputException('Thread UID is not matched with Response.');
            }
            $thread = $this->threadDao->getThreadByThreadUid($threadUid);
            $arcResponse = new ArcResponse(
                $arcResponseUid,
                $response->getThreadUid(),
                $response->getResponseUid(),
                $response->getSequence(),
                $response->getUserName(),
                $response->getUserId(),
                $response->getIp(),
                $response->getCreateDate(),
                $response->getContent(),
                $response->getAttachment(),
                new \DateTime()
            );
            if ($thread->getPassword() !== hash('sha256', $threadPassword)) {
                throw new InvalidUserInputException('User password is not matched with Thread password.');
            }
            $this->arcResponseDao->createArcResponse($arcResponse);
            $this->responseDao->deleteResponse($responseUid);
            $this->dataSource->commit();
        } catch (\PDOException $e) {
            $this->dataSource->rollBack();
            throw $e;
        } catch (InvalidUserInputException $e) {
            $this->dataSource->rollBack();
            throw $e;
        } catch (DataAccessException $e) {
            $this->dataSource->rollBack();
            throw $e;
        }
    }
}
