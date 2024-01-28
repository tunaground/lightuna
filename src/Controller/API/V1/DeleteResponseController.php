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

class DeleteResponseController extends AbstractApiController
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

            $response = $this->threadService->getResponseById($this->input->id);
            if ($response->getSequence() === 0) {
                throw new InvalidUserInputException('invalid response sequence');
            }
            $thread = $this->threadService->getThreadById($response->getThreadId());
            if ($thread->getPassword() !== $this->input->password) {
                throw new InvalidUserInputException('wrong password');
            }

            $this->threadService->deleteResponseById($this->input->id);
            $logger->info("response({$this->input->id}) has been deleted");
            $httpResponse->setBody(json_encode(
                [
                    'status' => 'ok',
                    'message' => "response({$this->input->id}) has been deleted",
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