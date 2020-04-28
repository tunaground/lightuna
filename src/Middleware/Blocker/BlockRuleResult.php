<?php
namespace Lightuna\Middleware\Blocker;

/**
 * Class BlockRuleResult
 * @package Lightuna\Middleware\Blocker
 */
class BlockRuleResult
{
    /** @var bool */
    private $pass;
    /** @var string */
    private $message;

    /**
     * BlockRuleResult constructor.
     * @param bool $pass
     * @param string $message
     */
    public function __construct(bool $pass, string $message)
    {
        $this->pass = $pass;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function getPass(): bool
    {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}