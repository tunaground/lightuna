<?php

namespace Lightuna\Controller;

use Lightuna\Core\Context;
use Lightuna\Dao\MariadbResponseDao;
use Lightuna\Dao\MariadbThreadDao;
use Lightuna\Exception\QueryException;
use Lightuna\Http\HttpRequest;
use Lightuna\Object\Response;
use Lightuna\Object\Thread;
use Lightuna\Service\ThreadService;
use Lightuna\Util\TemplateRenderer;
use Lightuna\Http\HttpResponse;

class ThreadController extends AbstractController
{
    private ThreadService $threadService;

    public function __construct(TemplateRenderer $templateRenderer, Context $context)
    {
        parent::__construct($templateRenderer, $context);
        $this->threadService = new ThreadService(
            new MariadbThreadDao($context->getPdo()),
            new MariadbResponseDao($context->getPdo()),
        );
    }

    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse
    {
        $dateTime = new \DateTime();

        $thread = new Thread(
            null,
            $httpRequest->getPost('board_id'),
            $httpRequest->getPost('title'),
            $httpRequest->getPost('password'),
            $httpRequest->getPost('username'),
            false,
            false,
            $dateTime,
            $dateTime,
        );
        $response = new Response(
            null,
            null,
            null,
            $httpRequest->getPost('username'),
            null,
            $httpRequest->getIp(),
            $httpRequest->getPost('content'),
            "", // $request->getPost('attachment'),
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
