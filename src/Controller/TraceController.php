<?php

namespace Lightuna\Controller;

use Lightuna\Core\Context;
use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Thread;
use Lightuna\Service\BoardServiceInterface;
use Lightuna\Service\ThreadServiceInterface;
use Lightuna\Util\TemplateHelper;
use Lightuna\Util\TemplateRenderer;

class TraceController extends AbstractController
{
    private BoardServiceInterface $boardService;
    private ThreadServiceInterface $threadService;
    private TemplateHelper $templateHelper;

    public function __construct(
        Context                $context,
        TemplateRenderer       $templateRenderer,
        BoardServiceInterface  $boardService,
        ThreadServiceInterface $threadService,
    )
    {
        parent::__construct($context, $templateRenderer);
        $this->boardService = $boardService;
        $this->threadService = $threadService;
        $this->templateHelper = new TemplateHelper($this->templateRenderer);
    }

    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse
    {
        try {
            $arguments = $this->context->getArgument();
            $thread = $this->threadService->getThreadById($arguments['threadId']);
            $board = $this->boardService->getBoardById($thread->getBoardId());

            $responseCount = $this->threadService->getResponseCountByThreadId($thread->getId());
            $limit = $responseCount;
            $offset = 1;
            if (isset($arguments['start']) && $arguments['start'] === 'recent') {
                $limit = $board->getDisplayResponse();
                if ($responseCount > $limit) {
                    $offset = $responseCount - $limit;
                }
            } else {
                if (isset($arguments['start']) && $arguments['start'] > 0) {
                    $offset = $arguments['start'];
                    if (isset($arguments['end'])) {
                        $limit = $arguments['end'] - $offset + 1;
                        if ($limit < 0) {
                            $limit = 1;
                        }
                    } else {
                        $limit = 1;
                    }
                }
            }
            $responses = array_merge(
                $this->threadService->getResponses($thread->getId(), 1, 0),
                $this->threadService->getResponses($thread->getId(), $limit, $offset),
            );
            $body = $this->templateRenderer->render('page/trace.html', [
                'thread' => $this->templateHelper->drawThread(
                    $thread->getId(),
                    $this->templateHelper->drawThreadHeader($thread),
                    array_reduce($responses, function ($acc, $response) use ($board) {
                        return $acc . $this->templateHelper->drawResponse($this->context->getConfig(), $board, $response);
                    }),
                    $this->templateHelper->drawCreateResponse($board, $thread),
                )
            ]);
        } catch (QueryException $e) {
            $body = $this->templateRenderer->render('page/error.html', [
                'message' => 'database query error'
            ]);
        } catch (ResourceNotFoundException $e) {
            $body = $this->templateRenderer->render('page/error.html', [
                'message' => $e->getMessage()
            ]);
        }
        $httpResponse->setBody($body);
        return $httpResponse;
    }
}
