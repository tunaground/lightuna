<?php
namespace Lightuna\Util;

/**
 * Class ContextParser
 * @package Lightuna\Util
 */
class ContextParser
{
    /** @var string */
    private $format;
    /** @var int */
    private $prefixSize;
    /** @var int */
    private $subfixSize;

    /**
     * ContextParser constructor.
     * @param string $format
     * @param int $prefixSize
     * @param int $subfixSize
     */
    public function __construct(string $format = '/\{[A-Za-z0-9\_\.]+\}/', int $prefixSize = 1, int $subfixSize = -1)
    {
        $this->setFormat($format, $prefixSize, $subfixSize);
    }

    /**
     * @param string $format
     * @param int $prefixSize
     * @param int $subfixSize
     */
    public function setFormat(string $format, int $prefixSize, int $subfixSize)
    {
        $this->format = $format;
        $this->prefixSize = $prefixSize;
        $this->subfixSize = $subfixSize;
    }

    /**
     * @param string $message
     * @param array $replace
     * @return string
     */
    public function parse(string $message, array $replace): string
    {
        return preg_replace_callback($this->format, function ($matches) use ($replace) {
            $match = substr($matches[0], $this->prefixSize, $this->subfixSize);
            return $replace[$match];
        }, $message);
    }
}

