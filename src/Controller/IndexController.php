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

class IndexController extends AbstractController
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
            $board = $this->boardService->getBoardById($arguments['boardId']);
            $notice = $this->boardService->getNoticeByBoardId($board->getId());

            $threads = $this->threadService->getThreadsByBoardId($board->getId(), $board->getDisplayThreadList());

            $body = $this->templateRenderer->render('page/index.html', [
                'notice' => $notice->getContent(),
                'board_name' => $board->getName(),
                'threads' => array_reduce($threads, function ($acc, $thread) use ($board) {
                    /** @var Thread $thread */
                    $responseCount = $this->threadService->getResponseCountByThreadId($thread->getId());
                    $limit = $board->getDisplayResponse();
                    if ($responseCount > $limit) {
                        $offset = $responseCount - $limit;
                        $added = $this->threadService->getResponses($thread->getId(), $limit, $offset);
                    } else {
                        $added = [];
                    }
                    $responses = array_merge(
                        $this->threadService->getResponses($thread->getId(), 1, 0),
                        $added,
                    );
                    return $acc . $this->templateHelper->drawThread(
                            $thread->getId(),
                            $this->templateHelper->drawThreadHeader($thread),
                            array_reduce($responses, function ($acc, $response) use ($board) {
                                return $acc . $this->templateHelper->drawResponse($this->context->getConfig(), $board, $response, true);
                            }),
                            $this->templateHelper->drawCreateResponse($board, $thread),
                        );
                }, ""),
                'create_thread' => $this->templateHelper->drawCreateThread($board),
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
