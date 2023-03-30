<?php

namespace Lightuna\Service;

use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Exception\SystemException;
use Lightuna\Object\Response;

class AttachmentService
{
    /**
     * @throws SystemException
     * @throws InvalidUserInputException
     */
    public function uploadAttachment(Response $response, array $file)
    {
        if (!$this->checkError($file)) {
            throw new SystemException;
        } elseif (
            !$this->checkFileExists($file)
            || !$this->checkType($file)
            || !$this->checkSize($file)
            || !$this->checkSizeLimit($file)
            || !$this->CheckNameSize($file)
        ) {
            throw new InvalidUserInputException;
        }
        $this->createDirectory();
        $this->makeImageName($response, $file);
    }

    private function checkError(array $file): bool
    {
        return ($file['error'] === UPLOAD_ERR_OK);
    }

    private function checkFileExists(array $file): bool
    {
        return (file_exists($file['tmp_name']) === true);
    }

    private function checkType(array $file): bool
    {
        return (in_array($file['type'], $this->config['site']['allowFileType'], true) === true);
    }

    private function checkSize(array $file): bool
    {
        return ($file['size'] !== 0);
    }

    private function checkSizeLimit(array $file): bool
    {
        return ($file['size'] < $this->board['maxImageSize']);
    }

    private function CheckNameSize(array $file): bool
    {
        return (mb_strlen($file['name'], 'utf-8') < $this->config['maxImageNameLength']);
    }

    /**
     * @throws SystemException
     */
    private function createDirectory(string $uploadPath)
    {
        $imagePath = "{$uploadPath}/image";
        $thumbPath = "{$uploadPath}/thumb";
        foreach ([$imagePath, $thumbPath] as $path) {
            if (file_exists($path) === false) {
                if (mkdir($path, 0750, true) !== true) {
                    throw new SystemException();
                }
            }
        }
    }

    /**
     * @throws InvalidUserInputException
     */
    private function makeImageName(Response $response, array $file): string
    {
        $createdAt = $response->getCreatedAt()->format('Uv');
        $responseId = $response->getResponseId();
        $name = $file['name'];
        return htmlspecialchars("{$createdAt}-{$responseId}-{$name}");
    }
}
