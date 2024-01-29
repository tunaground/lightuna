<?php

namespace Lightuna\Controller\Action;

use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Exception\QueryException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\PostOption;
use Lightuna\Object\Response;
use Lightuna\Service\AttachmentServiceInterface;
use Lightuna\Service\BoardServiceInterface;
use Lightuna\Service\ThreadServiceInterface;
use Lightuna\Util\TemplateRenderer;

class CreateResponseController extends AbstractController
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
        if ($httpRequest->getPost('content') === '') {
            $httpResponse->setBody($this->templateRenderer->render('page/error.html', [
                'message' => 'empty content',
            ]));
            return $httpResponse;
        }

        $options = new PostOption(
            (!is_null($httpRequest->getPost('relay'))),
            (!is_null($httpRequest->getPost('rich'))),
            (!is_null($httpRequest->getPost('noup'))),
            (!is_null($httpRequest->getPost('aa'))),
        );

        $dateTime = new \DateTime();

        $thread = $this->threadService->getThreadById($httpRequest->getPost('thread_id'));
        $board = $this->boardService->getBoardById($thread->getBoardId());
        $responseId = $this->threadService->getNextResponseId();
        $username = ($httpRequest->getPost('username') == '')? $board->getDefaultUsername() : $httpRequest->getPost('username');

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
            $username,
            null,
            $httpRequest->getIp(),
            $httpRequest->getPost('content'),
            $attachment,
            $httpRequest->getPost('youtube'),
            $dateTime,
            null,
        );
        try {
            $this->threadService->createResponse($response, $options);
            $body = "BAAAAAAAAAAAA";
        } catch (QueryException $e) {
            $body = $this->templateRenderer->render('page/error.html', [
                'message' => 'database query error'
            ]);
        }
        $httpResponse->addHeader("Refresh:2; url={$httpRequest->getPost("return_uri")}");
        $httpResponse->setBody($body);
        return $httpResponse;
    }
}
