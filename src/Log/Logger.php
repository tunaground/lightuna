<?php

namespace Lightuna\Log;

use Lightuna\Stream\StreamInterface;
use Lightuna\Util\TemplateResolver;

class Logger
{
    private TemplateResolver $messageMaker;
    private StreamInterface $stream;

    public function __construct(TemplateResolver $messageMaker)
    {
        $this->messageMaker = $messageMaker;
    }

    public function setStream(StreamInterface $stream): void
    {
        $this->stream = $stream;
    }

    public function info($message, array $context = [])
    {
        $this->put(LogLevel::Info, $this->messageMaker->make($message, $context));
    }

    public function debug($message, array $context = [])
    {
        $this->put(LogLevel::Debug, $this->messageMaker->make($message, $context));
    }

    private function put(string $logLevel, string $message)
    {
        $log = new Log(new \DateTime(), $logLevel, $message);
        $this->stream->write($log->toJsonString());
    }
}
