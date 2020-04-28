<?php
namespace Lightuna\Middleware\Blocker\Rule;

use Lightuna\Common\SwitchTrait;
use Lightuna\Middleware\Blocker\BlockRuleInterface;
use Lightuna\Middleware\Blocker\BlockRuleResult;

/**
 * Class SessionAuthRule
 * @package Lightuna\Middleware\Blocker\Rule
 */
class SessionAuthRule implements BlockRuleInterface
{
    use SwitchTrait;

    /**
     * SessionAuthRule constructor.
     * @param bool $enabled
     */
    public function __construct(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param array $config
     * @param array $request
     * @return BlockRuleResult
     */
    public function check(array $config, array $request): BlockRuleResult
    {
        if (
            !isset($config['blocker']['sessionAuth']['key']) ||
            !is_string($config['blocker']['sessionAuth']['key']) ||
            $config['blocker']['sessionAuth']['key'] === ''
        ) {
            return new BlockRuleResult(false, 'invalid-config/blocker/session-auth');
        }
        if (
            !isset($_SESSION['sessionAuth']) ||
            $_SESSION['sessionAuth'] !== $config['blocker']['sessionAuth']['key']
        ) {
            return new BlockRuleResult(false, '/blocker/block/session-auth');
        }
    }
}