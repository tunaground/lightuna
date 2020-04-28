<?php
namespace Lightuna\Middleware\Blocker;

use Lightuna\Common\MiddlewareInterface;
use Lightuna\Common\SwitchTrait;
use Lightuna\Exception\LightunaErrorException;

/**
 * Class Blocker
 * @package Lightuna\Middleware\Blocker
 */
class Blocker implements MiddlewareInterface
{
    use SwitchTrait;

    /**
     * @var BlockRuleInterface[] $rules
     */
    private $rules;

    /**
     * Blocker constructor.
     * @param bool $enabled
     * @param BlockRuleInterface ...$rules
     */
    public function __construct(bool $enabled, BlockRuleInterface ...$rules)
    {
        $this->enabled = $enabled;
        $this->rules = $rules;
    }

    /**
     * @param array $config
     * @param array $request
     */
    public function handle(array $config, array $request)
    {
        foreach ($this->rules as $rule) {
            if ($rule->isEnabled()) {
                $result = $rule->check($config, $request);
                if ($result->getPass()) {
                    throw new \RuntimeException($result->getMessage());
                }
            }
        }
    }
}