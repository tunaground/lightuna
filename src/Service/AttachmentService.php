<?php

namespace Lightuna\Service;

use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Exception\SystemException;
use Lightuna\Object\Board;
use Lightuna\Object\Response;
use Lightuna\Object\Thread;
use Lightuna\Util\ThumbUtil;

class AttachmentService
{
    public function __construct(
        private array $config,
        private ThumbUtil $thumbUtil,
    )
    {
    }

    /**
     * @throws SystemException
     * @throws InvalidUserInputException
     */
    public function uploadAttachment(Board $board, int $threadId, int $responseId, array $file): string
    {
        if (!$this->checkError($file)) {
            throw new SystemException;
        } elseif (!$this->checkFileExists($file)) {
            throw new InvalidUserInputException('invalid file(not exists');
        } elseif (!$this->checkSize($file)) {
            throw new InvalidUserInputException('invalid file(size zero)');
        } elseif (!$this->checkType($file, explode(',', $board->getLimitAttachmentType()))) {
            throw new InvalidUserInputException('invalid file(file type)');
        } elseif (!$this->checkSizeLimit($file, $board->getLimitAttachmentSize())) {
            throw new InvalidUserInputException('invalid file(file size)');
        } elseif (!$this->CheckNameSize($file, $board->getLimitAttachmentName())) {
            throw new InvalidUserInputException('invalid file(file name)');
        }

        $basePath = $this->createDirectory($board->getId(), $threadId);
        $imageName = htmlspecialchars("{$responseId}-{$file['name']}");
        $imageDest = "{$basePath}/images/$imageName";
        $this->moveFile($file['tmp_name'], $imageDest);
        $this->thumbUtil->makeThumb("{$basePath}/thumbnails/", $imageName, $imageDest);

        return $imageName;
    }

    private function checkError(array $file): bool
    {
        return ($file['error'] === UPLOAD_ERR_OK);
    }

    private function checkFileExists(array $file): bool
    {
        return (file_exists($file['tmp_name']) === true);
    }

    private function checkType(array $file, array $allowedTypes): bool
    {
        return (in_array($file['type'], $allowedTypes, true) === true);
    }

    private function checkSize(array $file): bool
    {
        return ($file['size'] !== 0);
    }

    private function checkSizeLimit(array $file, int $limit): bool
    {
        return ($file['size'] < $limit);
    }

    private function CheckNameSize(array $file, int $limit): bool
    {
        return (mb_strlen($file['name'], 'utf-8') <= $limit);
    }

    /**
     * @throws SystemException
     */
    private function createDirectory(string $boardId, int $threadId): string
    {
        $basePath = "{$this->config['attachment']['path']}/{$boardId}/{$threadId}";
        $imagePath = "{$basePath}/images";
        $thumbPath = "{$basePath}/thumbnails";

        foreach ([$imagePath, $thumbPath] as $path) {
            if (file_exists($path) === false) {
                if (mkdir($path, 0750, true) !== true) {
                    throw new SystemException();
                }
            }
        }

        return $basePath;
    }

    private function moveFile(string $src, string $dest)
    {
        if (move_uploaded_file($src, $dest) !== true) {
            throw new SystemException();
        }
    }
}
