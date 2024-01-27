<?php

namespace Lightuna\Controller;

use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;

interface ControllerInterface
{
    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse;
}
