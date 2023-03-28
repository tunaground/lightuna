<?php

namespace Lightuna\Log;

use Lightuna\Stream\StreamInterface;
<<<<<<< HEAD
use Lightuna\Util\MessageMaker;

class Logger
{
    private MessageMaker $messageMaker;
    private StreamInterface $stream;

    public function __construct(MessageMaker $messageMaker)
=======
use Lightuna\Util\TemplateResolver;

class Logger
{
    private TemplateResolver $messageMaker;
    private StreamInterface $stream;

    public function __construct(TemplateResolver $messageMaker)
>>>>>>> develop2
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
<<<<<<< HEAD
=======

>>>>>>> develop2
