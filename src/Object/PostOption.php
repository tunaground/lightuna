<?php

namespace Lightuna\Object;

class PostOption
{
    public function __construct(
        private bool $relay,
        private bool $rich,
        private bool $noup,
        private bool $aa,
    ) {}

    public function isRelay(): bool
    {
        return $this->relay;
    }

    public function setRelay(bool $relay): void
    {
        $this->relay = $relay;
    }

    public function isRich(): bool
    {
        return $this->rich;
    }

    public function setRich(bool $rich): void
    {
        $this->rich = $rich;
    }

    public function isNoup(): bool
    {
        return $this->noup;
    }

    public function setNoup(bool $noup): void
    {
        $this->noup = $noup;
    }

    public function isAa(): bool
    {
        return $this->aa;
    }

    public function setAa(bool $aa): void
    {
        $this->aa = $aa;
    }
}