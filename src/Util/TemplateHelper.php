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
            'board_id' => $board->getId(),
            'return_uri' => "/index/{$board->getId()}",
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
            'thread_id' => $thread->getId(),
            'title' => $thread->getTitle(),
            'size' => 0,
            'username' => $thread->getUsername(),
            'created_at' => $thread->getCreatedAt()->format(DATETIME_FORMAT),
            'updated_at' => $thread->getUpdatedAt()->format(DATETIME_FORMAT),
        ]);
    }

    public function drawResponse(array $config, Board $board, Response $response): string
    {
        if ($response->getAttachment() !== '') {
            $attachment_base = "{$config['attachment']['expose_path']}/{$board->getId()}/{$response->getThreadId()}";
            $attachment_filename = pathinfo($response->getAttachment(), PATHINFO_FILENAME);
            $attachment_thumbnail = "{$attachment_base}/thumbnails/{$attachment_filename}.jpg";
            $attachment_image = "{$attachment_base}/images/{$response->getAttachment()}";
            $attachment = $this->templateRenderer->render('attachment.html', [
                'attachment_thumbnail' => $attachment_thumbnail,
                'attachment_image' => $attachment_image,
            ]);
        } else {
            $attachment = '';
        }
        return $this->templateRenderer->render('response.html', [
            'username' => $response->getUsername(),
            'id' => $response->getUserId(),
            'created_at' => $response->getCreatedAt()->format(DATETIME_FORMAT),
            'content' => $response->getContent(),
            'attachment' => $attachment,
        ]);
    }

    public function drawCreateResponse(Board $board, Thread $thread): string
    {
        return $this->templateRenderer->render('create_response.html', [
            'thread_id' => $thread->getId(),
            'return_uri' => "/index/{$board->getId()}",
        ]);
    }

    public function drawUpdateBoard(Board $board): string
    {
        return $this->templateRenderer->render('update_board.html', [
            'id' => $board->getId(),
            'name' => $board->getName(),
            'created_at' => $board->getCreatedAt()->format(DATETIME_FORMAT),
            'updated_at' => $board->getUpdatedAt()->format(DATETIME_FORMAT),
            'deleted_at' => (is_null($board->getDeletedAt())) ? "No" : $board->getDeletedAt()->format(DATETIME_FORMAT),
            'display_thread' => $board->getDisplayThread(),
            'display_thread_list' => $board->getDisplayThreadList(),
            'display_response' => $board->getDisplayResponse(),
            'display_response_line' => $board->getDisplayResponseLine(),
            'limit_title' => $board->getLimitTitle(),
            'limit_name' => $board->getLimitName(),
            'limit_content' => $board->getLimitContent(),
            'limit_response' => $board->getLimitResponse(),
            'limit_attachment_type' => $board->getLimitAttachmentType(),
            'limit_attachment_size' => $board->getLimitAttachmentSize(),
            'limit_attachment_name' => $board->getLimitAttachmentName(),
            'interval_response' => $board->getIntervalResponse(),
            'interval_duplicate_response' => $board->getIntervalDuplicateResponse(),
        ]);
    }
}