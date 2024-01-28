<?php

namespace Lightuna\Controller;

use Lightuna\Core\Context;
use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Board;
use Lightuna\Object\Response;
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

            echo "offset: $offset, limit: $limit";

            $first_sequence = $responses[1]->getSequence();;
            $last_sequence = $responses[array_key_last($responses)]->getSequence();;

            $prev_start = ($first_sequence - $board->getDisplayResponse() < 1) ? 1 : $first_sequence - $board->getDisplayResponse();
            $prev_end = $prev_start + $board->getDisplayResponse();

            $next_start = $last_sequence + 1;
            $next_end = $next_start + $board->getDisplayResponse();
            echo "next_start: $next_start, next_end: $next_end";
            if ($next_end > $responseCount) {
                $next_start = 'recent';
                $next_end = '';
            }

            echo "next_start: $next_start, next_end: $next_end";

            $prev_start = ($offset - $board->getDisplayResponse() < 1) ? 1 : $offset - $board->getDisplayResponse();
            $prev_end = $board->getDisplayResponse();

            $boards = $this->boardService->getBoards();
            $nav_list = array_reduce($boards, function ($acc, $board) {
                /* @var Board $board */
                return array_merge($acc, [['link' => "/index/{$board->getId()}", 'text' => "{$board->getName()}"]]);
            }, [
                ['link' => '#top', 'text' => 'Top'],
                ['link' => '#bottom', 'text' => 'bottom'],
                ['link' => "/index/{$board->getId()}", 'text' => "{$board->getName()}"],
                ['link' => "/trace/{$thread->getId()}", 'text' => "All"],
                ['link' => "/trace/{$thread->getId()}/recent", 'text' => "Recent"],
                ['link' => "/trace/{$thread->getId()}/{$prev_start}/{$prev_end}", 'text' => "Previous"],
                ['link' => "/trace/{$thread->getId()}/{$next_start}/{$next_end}", 'text' => "Next"],
            ]);

            $body = $this->templateRenderer->render('page/trace.html', [
                'nav' => $this->templateRenderer->render('nav.html', [
                    'nav_items' => array_reduce($nav_list, function ($acc, $nav) {
                        return $acc . $this->templateRenderer->render('nav_item.html', [
                                'link' => $nav['link'],
                                'text' => $nav['text'],
                            ]);
                    }, "")
                ]),
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
