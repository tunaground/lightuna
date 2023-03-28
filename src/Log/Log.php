<?php

namespace Lightuna\Log;

class Log
{
    private \DateTime $dateTime;
    private string $logLevel;
    private string $message;

    public function __construct(\DateTime $dateTime, string $logLevel, string $message)
    {
        $this->dateTime = $dateTime;
        $this->logLevel = $logLevel;
        $this->message = $message;
    }

    public function toJsonString(): string
    {
        return json_encode([
            'datetime' => $this->dateTime->format(DATETIME_FORMAT),
            'loglevel' => $this->logLevel,
            'message' => $this->message,
        ]);
    }
}
