<?php

namespace Lightuna\Stream;

interface StreamInterface
{
    public function write(string $message);
}
