<?php
namespace Lightuna\Util;

use Lightuna\Exception\SystemException;

/**
 * Class ThumbUtil
 * @package Lightuna\Util
 */
class ThumbUtil
{
    /**
     * @param string $uploadPath
     * @param string $imageName
     * @param string $fileName
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws SystemException
     */
    public function makeThumb(string $uploadPath, string $imageName, string $fileName)
    {
        $fileContents = file_get_contents($fileName);
        if ($fileContents === false) {
            throw new \InvalidArgumentException(MSG_FAIL_FILE_READ);
        }
        $sourceImage = imagecreatefromstring($fileContents);
        if ($sourceImage === false) {
            throw new \RuntimeException(MSG_FAIL_FILE_CREATE);
        }

        list($width, $height) = $this->getImageSize($sourceImage);
        list($thumbWidth, $thumbHeight) = $this->getThumbSize($width, $height);

        $virtualImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        if ($virtualImage === false) {
            throw new \RuntimeException(MSG_FAIL_VIRTUAL_IMAGE_CREATE);
        }
        $sampleArgs = [$virtualImage, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height];
        if (imagecopyresampled(...$sampleArgs) !== true) {
            throw new \RuntimeException(MSG_FAIL_IMAGE_RESAMPLE);
        }
        $thumbName = "{$uploadPath}/{$imageName}";
        if (imagejpeg($virtualImage, $thumbName) !== true) {
            throw new SystemException(MSG_FAIL_IMAGE_SAVE);
        }
    }

    /**
     * @param $sourceImage
     * @return array
     * @throws \InvalidArgumentException
     */
    private function getImageSize($sourceImage): array
    {
        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);
        if ($width === false || $height === false) {
            throw new \InvalidArgumentException(MSG_IMAGE_GET_SIZE_FAILED);
        }
        return [$width, $height];
    }

    /**
     * @param int $width
     * @param int $height
     * @return array
     */
    private function getThumbSize(int $width, int $height): array
    {
        $thumbWidth = $width;
        $thumbHeight = $height;
        if ($width > 300) {
            $thumbWidth = 300;
            $thumbHeight = 300 / $width * $height;
        }
        if ($thumbHeight > 200) {
            $thumbHeight = 200;
            $thumbWidth = 200 / $height * $width;
        }
        return [$thumbWidth, $thumbHeight];
    }
}
