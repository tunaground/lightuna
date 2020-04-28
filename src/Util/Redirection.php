<?php
namespace Lightuna\Util;

/**
 * Class Redirection
 * @package Lightuna\Util
 */
class Redirection
{
    /**
     * @param string $page
     */
    public static function temporary(string $page): void
    {
        header(sprintf('Location: %s', $page), true, 302);
        die();
    }

    /**
     * @param string $page
     */
    public static function permanently(string $page): void
    {
        header(sprintf('Location: %s', $page), true, 301);
        die();
    }
}