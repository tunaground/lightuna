<?php

namespace Lightuna\Controller;

use Lightuna\Core\Context;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Util\TemplateRenderer;

abstract class AbstractApiController extends AbstractController
{
    protected Object $input;

    public function __construct(Context $context, TemplateRenderer $templateRenderer)
    {
        parent::__construct($context, $templateRenderer);
        $this->input = json_decode(file_get_contents('php://input'));
    }
}