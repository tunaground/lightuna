<?php
namespace Lightuna\Util;

use Lightuna\Exception\InvalidUserInputException;

/**
 * Class UriParser
 * @package Lightuna\Util
 */
class UriParser
{
    /** @var array */
    private $config;
    /** @var int */
    private $startIndex;
    /** @var array */
    private $uri;

    /**
     * UriParser constructor.
     * @param array $config
     * @param string $uri
     */
    public function __construct(array $config, string $uri)
    {
        $this->config = $config;
        $this->startIndex = ($this->config['site']['baseUrl'] === '') ? 1 : 2;
        $this->uri = explode('/', ltrim(strtok($uri, '?'), '/'));
    }

    /**
     * @return string
     * @throws InvalidUserInputException
     */
    public function getBoardUid(): string
    {
        try {
            return $this->getPartByIndex(0);
        } catch (\OutOfBoundsException $e) {
            throw new InvalidUserInputException('Invalid Board UID');
        }
    }

    /**
     * @return int
     * @throws InvalidUserInputException
     */
    public function getThreadUid(): int
    {
        try {
            return (int)$this->getPartByIndex(1);
        } catch (\OutOfBoundsException $e) {
            throw new InvalidUserInputException('Invalid Thread UID.');
        }
    }

    /**
     * @return int
     * @throws \OutOfBoundsException
     */
    public function getResponseStart(): int
    {
        try {
            return (int)$this->getPartByIndex(2);
        } catch (\OutOfBoundsException $e) {
            throw $e;
        }
    }

    /**
     * @param int $responseStart
     * @return int
     */
    public function getResponseEnd(int $responseStart): int
    {
        try {
            $responseEnd = (int)$this->getPartByIndex(3);
            if ($responseEnd < $responseStart) {
                return $responseStart;
            } else {
                return $responseEnd;
            }
        } catch (\OutOfBoundsException $e) {
            return $responseStart;
        }
    }

    /**
     * @return bool
     */
    public function isTraceRecent(): bool
    {
        try {
            return ($this->getPartByIndex(2) === 'recent');
        } catch (\OutOfBoundsException $e) {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getListPage(): int
    {
        try {
            $listPage = (int)$this->getPartByIndex(1);
            return ($listPage < 1) ? 1 : $listPage;
        } catch (\OutOfBoundsException $e) {
            return 1;
        }
    }

    /**
     * @param int $index
     * @return string
     * @throws \OutOfBoundsException
     */
    private function getPartByIndex(int $index): string
    {
        if (
            isset($this->uri[$this->startIndex + $index]) &&
            !empty($this->uri[$this->startIndex + $index])
        ) {
            return $this->uri[$this->startIndex + $index];
        } else {
            throw new \OutOfBoundsException('Index not exist.', 1);
        }
    }
}
