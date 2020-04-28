<?php
namespace Lightuna\Middleware\Blocker\Rule;

use Lightuna\Common\SwitchTrait;
use Lightuna\Middleware\Blocker\BlockRuleInterface;
use Lightuna\Middleware\Blocker\BlockRuleResult;

/**
 * Class CountryRule
 * @package Lightuna\Middleware\Blocker\Rule
 */
class CountryRule implements BlockRuleInterface
{
    use SwitchTrait;

    /**
     * CountryRule constructor.
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
        if (extension_loaded('geoip') === false) {
            return new BlockRuleResult(false, 'dependency/module/geoip');
        }
        if (!isset($config['blocker']['allowCountry']) || !is_array($config['blocker']['allowCountry'])) {
            return new BlockRuleResult(false, 'invalid-config/blocker/allow-country');
        }
        $countryCode = geoip_country_code_by_name($request['REMOTE_ADDR']);
        if (in_array($countryCode, $config['blocker']['allowCountry'], true)) {
            return new BlockRuleResult(false, 'blocker/disallowed-country');
        }
    }
}
