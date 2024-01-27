<?php

namespace Lightuna\Service;

use Lightuna\Object\Board;

interface AttachmentServiceInterface
{
    public function uploadAttachment(Board $board, int $threadId, int $responseId, array $file): string;
}