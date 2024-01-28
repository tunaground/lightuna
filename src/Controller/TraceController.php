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

            $first_sequence = (isset($responses[1])) ? $responses[1]->getSequence() : 0;
            $last_sequence = $responses[array_key_last($responses)]->getSequence();;

            $prev_start = ($first_sequence - $board->getDisplayResponse() < 1) ? 1 : $first_sequence - $board->getDisplayResponse();
            $prev_end = $prev_start + $board->getDisplayResponse();

            $next_start = $last_sequence + 1;
            $next_end = $next_start + $board->getDisplayResponse();
            if ($next_end > $responseCount) {
                $next_start = 'recent';
                $next_end = '';
            }

            $prev_start = ($offset - $board->getDisplayResponse() < 1) ? 1 : $offset - $board->getDisplayResponse();
            $prev_end = $board->getDisplayResponse();

            $boards = $this->boardService->getBoards();
            $nav_list = array_reduce($boards, function ($acc, $board) {
                /* @var Board $board */
                return array_merge($acc, [['link' => "/index/{$board->getId()}", 'text' => "{$board->getName()}", 'icon' => 'shuffle']]);
            }, [
                ['link' => '#top', 'text' => '', 'icon' => 'arrow-up'],
                ['link' => '#bottom', 'text' => '', 'icon' => 'arrow-down'],
                ['link' => "/index/{$board->getId()}", 'text' => "", 'icon' => 'home'],
                ['link' => "/trace/{$thread->getBoardId()}/{$thread->getId()}", 'text' => "", 'icon' => 'playlist-play'],
                ['link' => "/trace/{$thread->getBoardId()}/{$thread->getId()}/recent", 'text' => "", 'icon' => 'repeat'],
                ['link' => "/trace/{$thread->getBoardId()}/{$thread->getId()}/{$prev_start}/{$prev_end}", 'text' => "", 'icon' => 'skip-prev'],
                ['link' => "/trace/{$thread->getBoardId()}/{$thread->getId()}/{$next_start}/{$next_end}", 'text' => "", 'icon' => 'skip-next'],
            ]);

            $body = $this->templateRenderer->render('page/trace.html', [
                'nav' => $this->templateRenderer->render('nav.html', [
                    'nav_items' => array_reduce($nav_list, function ($acc, $nav) {
                        return $acc . $this->templateRenderer->render('nav_item.html', [
                                'link' => $nav['link'],
                                'text' => $nav['text'],
                                'icon' => $nav['icon'],
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
