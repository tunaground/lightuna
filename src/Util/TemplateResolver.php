<?php

namespace Lightuna\Util;

class TemplateResolver
{
    protected string $format;
    protected int $prefixSize;
    protected int $subfixSize;

    public function __construct(string $format = '/\{[A-Za-z0-9\_\.]+\}/', int $prefixSize = 1, int $subfixSize = -1)
    {
        $this->setFormat($format, $prefixSize, $subfixSize);
    }

    public function setFormat(string $format, int $prefixSize, int $subfixSize)
    {
        $this->format = $format;
        $this->prefixSize = $prefixSize;
        $this->subfixSize = $subfixSize;
    }

    public function make(string $message, array $replace): string
    {
        try {
            return preg_replace_callback($this->format, function ($matches) use ($replace) {
                $match = substr($matches[0], $this->prefixSize, $this->subfixSize);
                if (array_key_exists($match, $replace) !== true) {
                    throw new \Exception();
                }
                return $replace[$match];
            }, $message);
        } catch (\Throwable $e) {
            echo $e->getTraceAsString();
        }
    }
}

