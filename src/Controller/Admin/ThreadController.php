<?php

namespace Lightuna\Controller\Admin;

use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Dao\MariadbBoardDao;
use Lightuna\Dao\MariadbResponseDao;
use Lightuna\Dao\MariadbThreadDao;
use Lightuna\Exception\QueryException;
use Lightuna\Exception\ResourceNotFoundException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Thread;
use Lightuna\Service\BoardService;
use Lightuna\Service\ThreadService;
use Lightuna\Util\TemplateHelper;
use Lightuna\Util\TemplateRenderer;

class ThreadController extends AbstractController
{
    private BoardService $boardService;
    private ThreadService $threadService;
    private TemplateHelper $templateHelper;

    public function __construct(TemplateRenderer $templateRenderer, Context $context)
    {
        parent::__construct($templateRenderer, $context);
        $this->boardService = new BoardService(new MariadbBoardDao($this->context->getPdo()));
        $this->threadService = new ThreadService(
            new MariadbThreadDao($this->context->getPdo()),
            new MariadbResponseDao($this->context->getPdo()),
        );
        $this->templateHelper = new TemplateHelper($this->templateRenderer);
    }

    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse
    {
        try {
            $arguments = $this->context->getArgument();
            $board = $this->boardService->getBoardByName($arguments['boardName']);
            $threads = $this->threadService->getThreadsByBoardId($board->getBoardId(), $board->getThreadLimit());
            $body = $this->templateRenderer->render('page/admin/thread.html', [
                'board_name' => $board->getName(),
                'thread_list' => array_reduce($threads, function ($acc, $thread) use ($board) {
                    /** @var Thread $thread */
                    return $acc . $this->templateHelper->drawThread(
                            $this->templateHelper->drawThreadHeader($thread),
                            "",
                            ""
                        );
                }),
            ]);
        } catch (QueryException $e) {
            $body = $this->templateRenderer->render('error.html', [
                'message' => 'database query error'
            ]);
        } catch (ResourceNotFoundException $e) {
            $body = $this->templateRenderer->render('error.html', [
                'message' => $e->getMessage()
            ]);
        }
        $httpResponse->setBody($body);
        return $httpResponse;
    }
}
