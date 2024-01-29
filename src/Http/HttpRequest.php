<?php

namespace Lightuna\Http;

use Lightuna\Exception\InvalidUserInputException;

class HttpRequest
{
    public function __construct(
        private readonly array $server,
        private readonly array $post,
        private readonly array $get,
        private readonly array $file,
    )
    {
    }

    public function getPost(string $key)
    {
        if (array_key_exists($key, $this->post)) {
            return trim(htmlspecialchars($this->post[$key]));
        } else {
            return null;
        }
    }

    public function getQueryParam(string $key)
    {
        return $this->get[$key];
    }

    public function getFile(string $key)
    {
        return $this->file[$key];
    }

    public function getIp(): string
    {
        return (in_array("X-Forwarded-For", $this->server, true))
            ? $this->server["X-Forwarded-For"]
            : $this->server["REMOTE_ADDR"];
    }

    public function getRequestUri(): string
    {
        return $this->server["REQUEST_URI"];
    }

    public function getProtocol(): string
    {
        return $this->server['SERVER_PROTOCOL'];
    }

    public function getUserAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'];
    }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'];
    }
}
