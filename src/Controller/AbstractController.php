<?php

namespace Lightuna\Controller;

use Lightuna\Core\Context;
use Lightuna\Http\HttpResponse;
use Lightuna\Util\TemplateRenderer;

abstract class AbstractController implements ControllerInterface
{
    protected TemplateRenderer $templateRenderer;
    protected Context $context;

    public function __construct(TemplateRenderer $templateRenderer, Context $context)
    {
        $this->templateRenderer = $templateRenderer;
        $this->context = $context;
    }
}
