<?php

namespace Lightuna\Controller\API\V1;

use Lightuna\Controller\AbstractApiController;
use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Service\ThreadServiceInterface;
use Lightuna\Util\TemplateRenderer;

class GetResponseController extends AbstractApiController
{
    private ThreadServiceInterface $threadService;

    public function __construct(
        Context $context,
        TemplateRenderer $templateRenderer,
        ThreadServiceInterface $threadService,
    )
    {
        parent::__construct($context, $templateRenderer);
        $this->threadService = $threadService;
    }

    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse
    {
        try {
            $logger = $this->context->getLogger();

            $logger->info("test");
            $logger->info(print_r($this->input, true));

            $threadId = $this->input->threadId;
            $offset = $this->input->start;
            $limit = $this->input->end - $this->input->start + 1;

            $responses = $this->threadService->getResponsesByThreadId($threadId, $limit, $offset);
            $responses = array_reduce($responses, function ($acc, $response) {
                $response = $response->toArray();
                unset($response['ip']);
                return array_merge(
                    $acc,
                    [$response],
                );
            }, []);
            $httpResponse->setBody(json_encode(
                [
                    'status' => 'ok',
                    'responses' => $responses
                ],
                JSON_UNESCAPED_UNICODE
            ));
        } catch (\Throwable $e) {
            $httpResponse->setBody(json_encode(
                [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ],
                JSON_UNESCAPED_UNICODE
            ));
        }
        return $httpResponse;
    }
}