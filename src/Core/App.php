<?php

namespace Lightuna\Core;

use Lightuna\Database\DataSourceInterface;
use Lightuna\Database\Mariadb;
use Lightuna\Exception\NoRouteException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Log\Logger;
use Lightuna\Route\Router;
use Lightuna\Util\Redirect;
use Lightuna\Util\TemplateRenderer;

class App
{
    private array $config;
    private Logger $logger;
    private Router $router;

    public function __construct(array $config, Logger $logger, Router $router)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->router = $router;
        if ($this->config['site']['debug']) {
            $this->logger->debug("app running");
        }
    }

    public function run(HttpRequest $request)
    {
        $context = new Context();
        $dataSource = $this->getDataSource();
        $context->setPdo($dataSource->getConnection());
        $httpResponse = $this->route($request, $context);
        $httpResponse->send();
    }

    private function getDataSource(): DataSourceInterface
    {
        switch ($this->config['database']['type']) {
        case 'mariadb':
            return new Mariadb(
                $this->config['database']['host'],
                $this->config['database']['port'],
                $this->config['database']['user'],
                $this->config['database']['password'],
                $this->config['database']['schema'],
                $this->config['database']['options'],
            );
        }
    }

    /**
     * @throws NoRouteException
     */
    private function route(HttpRequest $httpRequest, Context $context): HttpResponse
    {
        $httpResponse = new HttpResponse();
        $route = $this->router->getRoute($httpRequest->getRequestUri());
        $context->setArgument($this->router->getArguments());
        if (array_key_exists('redirect', $route)) {
            $httpResponse->addHeader(Redirect::temporary($route['redirect']));
            return $httpResponse;
        } else {
            $templateRenderer = new TemplateRenderer($this->config['site']['rootDir'] . '/template/');
            $controller = new $route['controller']($templateRenderer, $context);
            return $controller->run($httpRequest, $httpResponse);
        }
    }
}
