<?php
namespace Lightuna\Common;

trait SwitchTrait
{
    protected $enabled;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}