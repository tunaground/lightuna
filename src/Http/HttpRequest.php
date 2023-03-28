<?php

namespace Lightuna\Http;

class HttpRequest
{
    public function __construct(
        private readonly array $server,
        private readonly array $post,
        private readonly array $get,
    )
    {
    }

    public function getPost(string $key)
    {
        return $this->post[$key];
    }

    public function getQueryParam(string $key)
    {
        return $this->get[$key];
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
}
