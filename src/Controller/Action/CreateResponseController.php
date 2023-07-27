<?php

namespace Lightuna\Controller\Action;

use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Dao\MariadbBoardDao;
use Lightuna\Dao\MariadbResponseDao;
use Lightuna\Dao\MariadbThreadDao;
use Lightuna\Exception\QueryException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Response;
use Lightuna\Service\AttachmentService;
use Lightuna\Service\BoardService;
use Lightuna\Service\ThreadService;
use Lightuna\Util\TemplateRenderer;
use Lightuna\Util\ThumbUtil;

class CreateResponseController extends AbstractController
{
    private BoardService $boardService;
    private ThreadService $threadService;
    private AttachmentService $attachmentService;

    public function __construct(TemplateRenderer $templateRenderer, Context $context)
    {
        parent::__construct($templateRenderer, $context);
        $this->boardService = new BoardService(
            new MariadbBoardDao($context->getPdo()),
        );
        $this->threadService = new ThreadService(
            new MariadbThreadDao($context->getPdo()),
            new MariadbResponseDao($context->getPdo()),
        );
        $this->attachmentService = new AttachmentService(
            $context->getConfig(),
            new ThumbUtil(),
        );
    }

    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse
    {
        $dateTime = new \DateTime();

        $thread = $this->threadService->getThreadById($httpRequest->getPost('thread_id'));
        $board = $this->boardService->getBoardById($thread->getBoardId());
        $responseId = $this->threadService->getNextResponseId();

        $attachment = ($httpRequest->getFile('attachment')['error'] !== UPLOAD_ERR_NO_FILE)
            ? $this->attachmentService->uploadAttachment(
                $board,
                $thread->getId(),
                $responseId,
                $httpRequest->getFile('attachment')
            )
            : "";

        $response = new Response(
            $responseId,
            $httpRequest->getPost('thread_id'),
            null,
            $httpRequest->getPost('username'),
            null,
            $httpRequest->getIp(),
            $httpRequest->getPost('content'),
            $attachment,
            $httpRequest->getPost('youtube'),
            false,
            $dateTime,
            null,
        );
        try {
            $this->threadService->createResponse($response);
            $body = "BAAAAAAAAAAAA";
        } catch (QueryException $e) {
            $body = $this->templateRenderer->render('page/admin/error.html', [
                'message' => 'database query error'
            ]);
        }
        $httpResponse->addHeader("Refresh:2; url={$httpRequest->getPost("return_uri")}");
        $httpResponse->setBody($body);
        return $httpResponse;
    }
}
