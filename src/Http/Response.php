<?php

namespace Lightuna\Http;

class Response
{
    private array $headers;
    private string $body;

    public function __construct()
    {
        $this->headers = [];
        $this->body = "";
    }

    public function addHeader(string $header)
    {
        $this->headers[] = $header;
    }

    public function setBody(string $body) {
        $this->body = $body;
    }

    public function send()
    {
        $this->sendHeaders();
        $this->sendBody();
    }

    private function sendHeaders()
    {
        foreach ($this->headers as $header) {
            header($header);
        }
    }

    private function sendBody()
    {
        echo $this->body;
    }
}