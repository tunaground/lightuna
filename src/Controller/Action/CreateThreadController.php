<?php

namespace Lightuna\Controller\Action;

use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Exception\QueryException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Response;
use Lightuna\Object\Thread;
use Lightuna\Service\AttachmentServiceInterface;
use Lightuna\Service\BoardServiceInterface;
use Lightuna\Service\ThreadServiceInterface;
use Lightuna\Util\TemplateRenderer;

class CreateThreadController extends AbstractController
{
    private BoardServiceInterface $boardService;
    private ThreadServiceInterface $threadService;
    private AttachmentServiceInterface $attachmentService;

    public function __construct(
        Context                    $context,
        TemplateRenderer           $templateRenderer,
        BoardServiceInterface      $boardService,
        ThreadServiceInterface     $threadService,
        AttachmentServiceInterface $attachmentService,
    )
    {
        parent::__construct($context, $templateRenderer);
        $this->boardService = $boardService;
        $this->threadService = $threadService;
        $this->attachmentService = $attachmentService;
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
            $this->threadService->createThread($thread, $response);
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
