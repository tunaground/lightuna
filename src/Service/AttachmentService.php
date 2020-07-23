<?php
namespace Lightuna\Service;

use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Exception\SystemException;
use Lightuna\Object\Board;
use Lightuna\Util\ThumbUtil;

/**
 * Class AttachmentService
 * @package Lightuna\Service
 */
class AttachmentService
{
    /** @var array */
    private $config;
    /** @var Board */
    private $board;
    /** @var ThumbUtil */
    private $thumbUtil;

    /**
     * AttachmentService constructor.
     * @param array $config
     * @param Board $board
     * @param ThumbUtil $thumbUtil
     */
    public function __construct(array $config, Board $board, ThumbUtil $thumbUtil)
    {
        $this->config = $config;
        $this->board = $board;
        $this->thumbUtil = $thumbUtil;
    }

    /**
     * @param array $fileRequest
     * @return string
     * @throws SystemException
     * @throws InvalidUserInputException
     */
    public function upload(array $fileRequest): string
    {
        if (!$this->checkError($fileRequest)) {
            throw new SystemException(MSG_FILE_UPLOAD_FAILED);
        } elseif (!$this->checkFileExists($fileRequest)) {
            throw new InvalidUserInputException(MSG_FILE_UPLOAD_FAILED);
        } elseif (!$this->checkType($fileRequest)) {
            throw new InvalidUserInputException(MSG_LIMIT_FILE_TYPE);
        } elseif (!$this->checkSize($fileRequest)) {
            throw new InvalidUserInputException(MSG_INVALID_FILE);
        } elseif (!$this->checkSizeLimit($fileRequest)) {
            throw new InvalidUserInputException(MSG_LIMIT_FILE_SIZE);
        }
        $this->createDirectory($this->config['site']['imageUploadPath']);
        $imageName = $this->makeImageName($fileRequest);
        $fileName = $this->config['site']['imageUploadPath'] . '/image/' . $imageName;
        if (move_uploaded_file($fileRequest['tmp_name'], $fileName) !== true) {
            throw new SystemException(MSG_FILE_MOVE_FAILED);
        }
        $this->thumbUtil->makeThumb(
            $this->config['site']['imageUploadPath'] . '/thumb',
            $imageName,
            $fileName
        );
        return $imageName;
    }

    private function checkFileExists(array $fileRequest): bool
    {
        return (file_exists($fileRequest['tmp_name']) === true);
    }

    private function checkError(array $fileRequest): bool
    {
        return ($fileRequest['error'] === UPLOAD_ERR_OK);
    }

    private function checkType(array $fileRequest): bool
    {
        return (in_array($fileRequest['type'], $this->config['site']['allowFileType'], true) === true);
    }

    private function checkSize(array $fileRequest): bool
    {
        return ($fileRequest['size'] !== 0);
    }

    private function checkSizeLimit(array $fileRequest): bool
    {
        return ($fileRequest['size'] < $this->board['maxImageSize']);
    }

    /**
     * @param string $uploadPath
     * @throws SystemException
     */
    private function createDirectory(string $uploadPath)
    {
        $imagePath = "{$uploadPath}/image";
        $thumbPath = "{$uploadPath}/thumb";
        foreach ([$imagePath, $thumbPath] as $path) {
            if (file_exists($path) === false) {
                if (mkdir($path, 0750, true) !== true) {
                    throw new SystemException(MSG_DIRECTORY_CREATE_FAILED);
                }
            }
        }
    }

    /**
     * @param array $fileRequest
     * @return string
     * @throws InvalidUserInputException
     */
    private function makeImageName(array $fileRequest): string
    {
        $time = round(microtime(true) * 1000);
        $rand = mt_rand();
        $name = $fileRequest['name'];
        $imageName = "{$time}-{$rand}-{$name}";
        $imageName = htmlspecialchars($imageName);
        if (mb_strlen($imageName, 'utf-8') > $this->board['maxImageNameLength']) {
            throw new InvalidUserInputException(MSG_LIMIT_FILENAME_LENGTH);
        }
        return $imageName;
    }
}
