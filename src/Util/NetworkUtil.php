<?php
namespace Lightuna\Util;

/**
 * Class NetworkUtil
 * @package Lightuna\Util
 */
class NetworkUtil
{
    /**
     * @return string
     */
    public function getIP(): string
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            return $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        return $_SERVER['REMOTE_ADDR'];
    }
}
