<?php

namespace Lightuna\Util;

class Redirect
{
    public static function temporary(string $uri): string
    {
        return "Location: {$uri}";
    }
}
<<<<<<< HEAD
=======

>>>>>>> develop2
