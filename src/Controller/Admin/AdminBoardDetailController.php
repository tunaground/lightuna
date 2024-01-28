<?php

namespace Lightuna\Controller\Admin;

use Lightuna\Controller\AbstractController;
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

class AdminBoardDetailController extends AbstractController
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
            $body = $this->templateRenderer->render('page/admin/board.html', [
                'board_id' => $board->getId(),
                'board_name' => $board->getName(),
                'board_config' => $this->templateHelper->drawUpdateBoard($board),
                'notice_config' => $this->templateHelper->drawUpdateNotice($notice),
                'thread_list' => array_reduce($threads, function ($acc, $thread) {
                    /** @var Thread $thread */
                    $deletedAt = ($thread->getDeletedAt() === null)? null : $thread->getDeletedAt()->format(DATETIME_FORMAT);
                    return $acc . $this->templateRenderer->render('admin_thread.html', [
                            'id' => $thread->getId(),
                            'title' => $thread->getTitle(),
                            'username' => $thread->getUsername(),
                            'created_at' => $thread->getCreatedAt()->format(DATETIME_FORMAT),
                            'deleted_at' => $deletedAt,
                        ]);
                }),
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
