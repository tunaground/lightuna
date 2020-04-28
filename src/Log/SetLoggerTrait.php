<?php
namespace Lightuna\Log;

/**
 * Trait SetLoggerTrait
 * @package Lightuna\Log
 */
trait SetLoggerTrait
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
}