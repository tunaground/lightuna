<?php

namespace Lightuna\Route;

use Lightuna\Exception\NoRouteException;

class Router
{
    private array $routeConfig;
    private array $arguments;

    public function __construct(array $routeConfig)
    {
        $this->routeConfig = $routeConfig;
        $this->arguments = [];
    }

    /**
     * @throws NoRouteException
     */
    public function getRoute(string $requestUri): array
    {
        foreach ($this->routeConfig as $route) {
            if ($this->match($route['path'], $requestUri)) {
                return $route;
            }
        }
        throw new NoRouteException();
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    private function compare(array $matchFactor, array $requestUriSeg): bool
    {
        if (sizeof($matchFactor) > sizeof($requestUriSeg)) {
            return false;
        }
        for ($i = 0; $i < sizeof($matchFactor); $i++) {
            if (preg_match('/:[a-zA-Z0-9]+/', $matchFactor[$i]) === 0
                && $matchFactor[$i] !== $requestUriSeg[$i]
            ) {
                return false;
            }
        }
        return true;
    }


    private function match(string $routeUri, string $requestUri): bool
    {
        $requestUriSeg = explode('/', preg_replace('/[\/]+/', '/', $requestUri));
        $matchFactor = explode('/', explode('?', $routeUri)[0]);
        array_shift($requestUriSeg);
        array_shift($matchFactor);
        if ($this->compare($matchFactor, $requestUriSeg)) {
            preg_match_all('/[\/\?]?:?([a-zA-Z0-9\.]+)/', $routeUri, $matches);
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                if (!str_contains($matches[0][$i], ':')) {
                    continue;
                } else {
                    if (isset($requestUriSeg[$i])) {
                        $this->arguments[$matches[1][$i]] = $requestUriSeg[$i];
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
