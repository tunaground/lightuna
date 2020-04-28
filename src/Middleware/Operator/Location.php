<?php
namespace Lightuna\Middleware\Operator;

use Lightuna\Common\MiddlewareInterface;
use Lightuna\Common\SwitchTrait;

/**
 * Class Location
 * @package Lightuna\Middleware\Operator
 */
class Location implements MiddlewareInterface
{
    use SwitchTrait;

    /** @var MiddlewareInterface[] $middlewares */
    private $middlewares;
    /** @var array */
    private $locations;

    /**
     * Location constructor.
     * @param bool $enabled
     * @param array $locations
     * @param MiddlewareInterface ...$middlewares
     */
    public function __construct(bool $enabled, array $locations, MiddlewareInterface ...$middlewares)
    {
        $this->enabled = $enabled;
        $this->locations = $locations;
        $this->middlewares = $middlewares;
    }

    /**
     * @param array $config
     * @param array $request
     */
    public function handle(array $config, array $request)
    {
        if (in_array($request['SCRIPT_NAME'], $this->locations, true)) {
            foreach ($this->middlewares as $middleware) {
                $middleware->handle($config, $request);
            }
        }
    }
}