<?php
namespace Lightuna\Log;

use Lightuna\Util\ContextParser;

/**
 * Class Logger
 * @package Lightuna\Log
 */
class Logger
{
    /** @var string */
    private $filePath;
    /** @var ContextParser */
    private $contextParser;

    /**
     * Logger constructor.
     * @param string $filePath
     * @param ContextParser $contextParser
     */
    public function __construct(string $filePath, ContextParser $contextParser)
    {
        $this->filePath = $filePath;
        $this->contextParser = $contextParser;
    }

    /**
     * @param $message
     * @param array $context
     */
    public function notice($message, array $context = [])
    {
        $this->put(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @param $message
     * @param array $context
     */
    public function warning($message, array $context = [])
    {
        $this->put(LogLevel::WARNING, $message, $context);
    }

    /**
     * @param $message
     * @param array $context
     */
    public function error($message, array $context = [])
    {
        $this->put(LogLevel::ERROR, $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->put(LogLevel::DEBUG, $message, $context);
    }

    /**
     * @param string $severity
     * @param $message
     * @param array $context
     */
    private function put(string $severity, $message, array $context = [])
    {
        try {
            $dateTime = new \DateTime();
            $message = $this->contextParser->parse($message, $context);
            $log = sprintf('[%s](%s) %s%s', $dateTime->format('Y-m-d H:i:s'), $severity, $message, PHP_EOL);
            $fp = fopen($this->filePath, 'a+');
            fwrite($fp, $log);
            fclose($fp);
        } catch (\Exception $e) {
            return;
        }
    }
}