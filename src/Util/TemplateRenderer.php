<?php

namespace Lightuna\Util;

class TemplateRenderer extends TemplateResolver
{
    private string $templateDir;

    public function __construct(string $templateDir)
    {
        parent::__construct('/\{\{[A-Za-z0-9\_\.]+\}\}/', 2, -2);
        $this->templateDir = $templateDir;
    }

    public function render(string $template, array $replace = []): string
    {
        $templatePath = $this->resolveTemplate($template);
        return $this->make(file_get_contents($templatePath), $replace);
    }

    private function resolveTemplate(string $template): string
    {
        return $this->templateDir . '/' . $template;
    }
}

