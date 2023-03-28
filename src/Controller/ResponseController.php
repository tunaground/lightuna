<?php

namespace Lightuna\Controller;

use Lightuna\Core\Context;
use Lightuna\Dao\MariadbResponseDao;
use Lightuna\Dao\MariadbThreadDao;
use Lightuna\Exception\QueryException;
use Lightuna\Http\HttpRequest;
use Lightuna\Object\Response;
use Lightuna\Service\ThreadService;
use Lightuna\Util\TemplateRenderer;
use Lightuna\Http\HttpResponse;

class ResponseController extends AbstractController
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

        $response = new Response(
            null,
            $httpRequest->getPost('thread_id'),
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
            $this->threadService->createReponse($response);
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
