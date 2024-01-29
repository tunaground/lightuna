<?php

namespace Lightuna\Service;

use Lightuna\Object\PostOption;
use Lightuna\Object\Response;
use Lightuna\Object\Thread;

interface ThreadServiceInterface
{
    public function getNextThreadId(): int;

    public function getNextResponseId(): int;

    public function createThread(Thread $thread, Response $response, PostOption $postOption): void;

    public function createResponse(Response $response, PostOption $postOption): void;

    public function getThreadsByBoardId(string $boardId, int $limit = 0, int $offset = 0): array;

    public function getThreadById(int $id): Thread;

    public function getResponsesByThreadId(int $threadId, int $limit = 0, int $offset = 0): array;

    public function getResponseCountByThreadId(int $threadId): int;

    public function deleteResponseById(int $id, string $password): Response;

    public function restoreResponseId(int $id, string $password): Response;

    public function getResponseById(int $id): Response;
}