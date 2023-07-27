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
use Lightuna\Object\Thread;
use Lightuna\Service\AttachmentService;
use Lightuna\Service\BoardService;
use Lightuna\Service\ThreadService;
use Lightuna\Util\TemplateRenderer;
use Lightuna\Util\ThumbUtil;

class CreateThreadController extends AbstractController
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
            $this->context->getConfig(),
            new ThumbUtil(),
        );
    }

    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse
    {
        $dateTime = new \DateTime();

        $board = $this->boardService->getBoardById($httpRequest->getPost('board_id'));

        $threadId = $this->threadService->getNextThreadId();
        $thread = new Thread(
            $threadId,
            $httpRequest->getPost('board_id'),
            $httpRequest->getPost('title'),
            $httpRequest->getPost('password'),
            $httpRequest->getPost('username'),
            false,
            false,
            $dateTime,
            $dateTime,
        );

        $responseId = $this->threadService->getNextResponseId();
        if ($httpRequest->getFile('attachment')['error'] !== UPLOAD_ERR_NO_FILE) {
            $attachment = $this->attachmentService->uploadAttachment(
                $board,
                $threadId,
                $responseId,
                $httpRequest->getFile('attachment'),
            );
        } else {
            $attachment = "";
        }

        $response = new Response(
            $responseId,
            $threadId,
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
            $this->threadService->createThread($this->context->getPdo(), $thread, $response);
            $body = "BAAAAAAAAAAAA";
        } catch (QueryException $e) {
            $body = $this->templateRenderer->render('error.html', [
                'message' => 'database query error'
            ]);
        }
        $httpResponse->addHeader("Refresh:2; url={$httpRequest->getPost("return_uri")}");
        $httpResponse->setBody($body);
        return $httpResponse;
    }
}
