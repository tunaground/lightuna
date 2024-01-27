<?php

namespace Lightuna\Controller;

use Lightuna\Core\Context;
use Lightuna\Util\TemplateRenderer;

abstract class AbstractController implements ControllerInterface
{
    protected Context $context;
    protected TemplateRenderer $templateRenderer;

    public function __construct(Context $context, TemplateRenderer $templateRenderer)
    {
        $this->context = $context;
        $this->templateRenderer = $templateRenderer;
    }
}
