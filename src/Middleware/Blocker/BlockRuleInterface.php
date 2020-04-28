<?php
namespace Lightuna\Middleware\Blocker;

use Lightuna\Common\SwitchInterface;

/**
 * Interface BlockRuleInterface
 * @package Lightuna\Middleware\Blocker
 */
interface BlockRuleInterface extends SwitchInterface
{
    /**
     * @param array $config
     * @param array $request
     * @return BlockRuleResult
     */
    public function check(array $config, array $request): BlockRuleResult;
}