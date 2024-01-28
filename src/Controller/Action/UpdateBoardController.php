<?php

namespace Lightuna\Controller\Action;

use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Exception\QueryException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Board;
use Lightuna\Object\Notice;
use Lightuna\Object\Response;
use Lightuna\Object\Thread;
use Lightuna\Service\AttachmentServiceInterface;
use Lightuna\Service\BoardServiceInterface;
use Lightuna\Service\ThreadServiceInterface;
use Lightuna\Util\TemplateRenderer;

class UpdateBoardController extends AbstractController
{
    private BoardServiceInterface $boardService;

    public function __construct(
        Context               $context,
        TemplateRenderer      $templateRenderer,
        BoardServiceInterface $boardService,
    )
    {
        parent::__construct($context, $templateRenderer);
        $this->boardService = $boardService;
    }

    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse
    {
        try {
            $logger = $this->context->getLogger();

            $dateTime = new \DateTime();
            $board = $this->boardService->getBoardById($httpRequest->getPost('id'));
            $board->setName($httpRequest->getPost('name'));
            $board->setDefaultUsername($httpRequest->getPost('default_username'));
            $board->setDisplayThread($httpRequest->getPost('display_thread'));
            $board->setDisplayThreadList($httpRequest->getPost('display_thread_list'));
            $board->setDisplayResponse($httpRequest->getPost('display_response'));
            $board->setDisplayResponseLine($httpRequest->getPost('display_response_line'));
            $board->setLimitTitle($httpRequest->getPost('limit_title'));
            $board->setLimitName($httpRequest->getPost('limit_name'));
            $board->setLimitContent($httpRequest->getPost('limit_content'));
            $board->setLimitResponse($httpRequest->getPost('limit_response'));
            $board->setLimitAttachmentType($httpRequest->getPost('limit_attachment_type'));
            $board->setLimitAttachmentSize($httpRequest->getPost('limit_attachment_size'));
            $board->setLimitAttachmentName($httpRequest->getPost('limit_attachment_name'));
            $board->setIntervalResponse($httpRequest->getPost('interval_response'));
            $board->setIntervalDuplicateResponse($httpRequest->getPost('interval_duplicate_response'));
            $board->setUpdatedAt($dateTime);
            $this->boardService->updateBoard($board);

            $boardId = $board->getId();

            $logger->info("board({$boardId}) updated");

            $httpResponse->addHeader("Refresh:0; url=/admin/board/{$boardId}");
        } catch (QueryException $e) {
            $body = $this->templateRenderer->render('page/error.html', [
                'message' => 'database query error'
            ]);
            $httpResponse->setBody($body);
        } catch (\Throwable $e) {
            $body = $this->templateRenderer->render('page/error.html', [
                'message' => $e->getMessage()
            ]);
            $httpResponse->setBody($body);
        }
        return $httpResponse;
    }
}
