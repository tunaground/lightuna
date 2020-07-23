<?php
namespace Lightuna\Service;

use Lightuna\Database\ArcResponseDaoInterface;
use Lightuna\Database\DataSource;
use Lightuna\Database\ResponseDaoInterface;
use Lightuna\Database\ThreadDaoInterface;
use Lightuna\Exception\DataAccessException;
use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Object\ArcResponse;
use Lightuna\Object\Response;

/**
 * Class ResponseService
 * @package Lightuna\Service
 */
class ResponseService
{
    /** @var DataSource */
    private $dataSource;
    /** @var ThreadDaoInterface */
    private $threadDao;
    /** @var ResponseDaoInterface */
    private $responseDao;
    /** @var ArcResponseDaoInterface */
    private $arcResponseDao;

    /**
     * ResponseService constructor.
     * @param DataSource $dataSource
     * @param ThreadDaoInterface $threadDao
     * @param ResponseDaoInterface $responseDao
     * @param ArcResponseDaoInterface $arcResponseDao
     */
    public function __construct(
        DataSource $dataSource,
        ThreadDaoInterface $threadDao,
        ResponseDaoInterface $responseDao,
        ArcResponseDaoInterface $arcResponseDao
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
                throw new InvalidUserInputException(MSG_INVALID_THREAD_UID);
            }
            if ((int)$response->getSequence() === 0) {
                throw new InvalidUserInputException(MSG_CANNOT_DELETE_FIRST_RESPONSE);
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
                $response->getYoutube(),
                new \DateTime()
            );
            if ($thread->getPassword() !== hash('sha256', $threadPassword)) {
                throw new InvalidUserInputException(MSG_INVALID_PASSWORD);
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

    /**
     * @param int $threadUid
     * @param int $responseUid
     * @param string $threadPassword
     * @throws \PDOException
     * @throws InvalidUserInputException
     * @throws DataAccessException
     */
    public function unarchiveResponse(int $threadUid, int $responseUid, string $threadPassword)
    {
        try {
            $this->dataSource->beginTransaction();
            $arcResponse = $this->arcResponseDao->getArcResponseByResponseUid($responseUid);
            if ((int)$arcResponse->getThreadUid() !== $threadUid) {
                throw new InvalidUserInputException(MSG_INVALID_THREAD_UID);
            }
            if ((int)$arcResponse->getSequence() === 0) {
                throw new InvalidUserInputException(MSG_CANNOT_DELETE_FIRST_RESPONSE);
            }
            $thread = $this->threadDao->getThreadByThreadUid($threadUid);
            $response = new Response(
                $arcResponse->getThreadUid(),
                $arcResponse->getResponseUid(),
                $arcResponse->getSequence(),
                $arcResponse->getUserName(),
                $arcResponse->getUserId(),
                $arcResponse->getIp(),
                $arcResponse->getCreateDate(),
                $arcResponse->getContent(),
                $arcResponse->getAttachment(),
                $arcResponse->getYoutube()
            );
            if ($thread->getPassword() !== hash('sha256', $threadPassword)) {
                throw new InvalidUserInputException(MSG_INVALID_PASSWORD);
            }
            $this->responseDao->createResponse($response);
            $this->arcResponseDao->deleteArcResponse($arcResponse->getArcResponseUid());
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
