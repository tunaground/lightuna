<?php

namespace Lightuna\Util;

use Lightuna\Object\Board;
use Lightuna\Object\Response;
use Lightuna\Object\Thread;

class TemplateHelper
{
    public function __construct(
        private readonly TemplateRenderer $templateRenderer
    )
    {}

    public function drawCreateThread(Board $board): string
    {
        return $this->templateRenderer->render('create_thread.html', [
            'board_id' => $board->getBoardId(),
            'return_uri' => "/index.php/{$board->getName()}",
        ]);
    }

    public function drawThread(string $threadHeader, string $responses, string $createResponse): string
    {
        return $this->templateRenderer->render('thread.html', [
            'thread_head' => $threadHeader,
            'responses' => $responses,
            'create_response' => $createResponse,
        ]);
    }

    public function drawThreadHeader(Thread $thread): string
    {
        return $this->templateRenderer->render('thread_head.html', [
            'thread_id' => $thread->getThreadId(),
            'title' => $thread->getTitle(),
            'size' => 0,
            'username' => $thread->getUsername(),
            'created_at' => $thread->getCreatedAt()->format(DATETIME_FORMAT),
            'updated_at' => $thread->getUpdatedAt()->format(DATETIME_FORMAT),
        ]);
    }

    public function drawResponse(Response $response): string
    {
        return $this->templateRenderer->render('response.html', [
            'username' => $response->getUsername(),
            'id' => $response->getUserId(),
            'created_at' => $response->getCreatedAt()->format(DATETIME_FORMAT),
            'content' => $response->getContent(),
        ]);
    }

    public function drawCreateResponse(Board $board, Thread $thread): string
    {
        return $this->templateRenderer->render('create_response.html', [
            'thread_id' => $thread->getThreadId(),
            'return_uri' => "/index.php/{$board->getName()}",
        ]);
    }
}