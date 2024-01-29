<?php

namespace Lightuna\Controller\API\V1;

use Lightuna\Controller\AbstractApiController;
use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Service\ThreadServiceInterface;
use Lightuna\Util\TemplateRenderer;

class RestoreResponseController extends AbstractApiController
{
    private ThreadServiceInterface $threadService;

    public function __construct(
        Context                $context,
        TemplateRenderer       $templateRenderer,
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

            $response = $this->threadService->restoreResponseId(
                $this->input->id,
                $this->input->password,
            );

            $arr = [
                'sequence' => $response->getSequence(),
                'username' => $response->getUsername(),
                'userId' => $response->getUserId(),
                'createdAt' => $response->getCreatedAt()->format(DATETIME_FORMAT),
                'content' => $response->getContent(),
                'youtube' => $response->getYoutube(),
                'attachment' => $response->getAttachment(),
            ];

            $logger->info("response({$this->input->id}) has been restored");
            $httpResponse->setBody(json_encode(
                [
                    'status' => 'ok',
                    'message' => "response({$this->input->id}) has been restored",
                    'data' => ['response' => $arr]
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