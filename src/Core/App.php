<?php

namespace Lightuna\Core;

use Lightuna\Exception\InvalidConfigException;
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
    }

    public function run(HttpRequest $request)
    {
        $this->logger->info('{ip} - {protocol} {method} {uri} - {user_agent}', [
            'ip' => $request->getIp(),
            'uri' => $request->getRequestUri(),
            'method' => $request->getMethod(),
            'protocol' => $request->getProtocol(),
            'user_agent' => $request->getUserAgent(),
        ]);

        $context = new Context();
        $context->setConfig($this->config);

        $this->route($request, $context);
    }

    private function getPDO(): \PDO
    {
        switch ($this->config['database']['type']) {
            case 'mariadb':
                $pdo = new \PDO(
                    sprintf(
                        'mysql:host=%s;port=%s;dbname=%s',
                        $this->config['database']['host'],
                        $this->config['database']['port'],
                        $this->config['database']['schema'],
                    ),
                    $this->config['database']['user'],
                    $this->config['database']['password'],
                    $this->config['database']['options'],
                );
                break;
            default:
                throw new InvalidConfigException();
        }
        return $pdo;
    }

    /**
     * @throws NoRouteException
     */
    private function route(HttpRequest $httpRequest, Context $context): void
    {
        $httpResponse = new HttpResponse();
        $route = $this->router->getRoute($httpRequest->getRequestUri());
        $context->setArgument($this->router->getArguments());
        if (array_key_exists('redirect', $route)) {
            $httpResponse->addHeader(Redirect::temporary($route['redirect']));
        } else {
            $templateRenderer = new TemplateRenderer($this->config['site']['rootDir'] . '/template');
            $controller = $route['controller']($context);
            $controller->run($httpRequest, $httpResponse);
        }
        $httpResponse->send();
    }
}
