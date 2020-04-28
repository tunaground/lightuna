<?php
namespace Lightuna\Common;

interface MiddlewareInterface extends SwitchInterface
{
    public function handle(array $config, array $request);
}