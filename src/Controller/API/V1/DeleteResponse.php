<?php

namespace Lightuna\Controller\API\V1;

use Lightuna\Controller\AbstractApiController;
use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Service\ThreadServiceInterface;
use Lightuna\Util\TemplateRenderer;

class DeleteResponse extends AbstractApiController
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
            $this->threadService->deleteResponseById($this->input->id);
            $logger->info("response({$this->input->id}) has been deleted");
            $httpResponse->setBody(json_encode(
                [
                    'status' => 'ok',
                    'message' => "response({$this->input->id}) has been deleted",
                ]
            ));
        } catch (\Throwable $e) {
            $httpResponse->setBody(json_encode(
                [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ]
            ));
        }
        return $httpResponse;
    }
}