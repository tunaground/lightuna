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
<<<<<<< HEAD
=======
        $this->arguments = [];
>>>>>>> develop2
    }

    /**
     * @throws NoRouteException
     */
    public function getRoute(string $requestUri): array
    {
        foreach ($this->routeConfig as $route) {
<<<<<<< HEAD
            if ($this->match($route['path'], $_SERVER['REQUEST_URI'])) {
=======
            if ($this->match($route['path'], $requestUri)) {
>>>>>>> develop2
                return $route;
            }
        }
        throw new NoRouteException();
    }

<<<<<<< HEAD
=======
    public function getArguments(): array
    {
        return $this->arguments;
    }

>>>>>>> develop2
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
<<<<<<< HEAD
            preg_match_all('/[\/\?]?:?([a-zA-Z0-9]+)/', $routeUri, $matches);
=======
            preg_match_all('/[\/\?]?:?([a-zA-Z0-9\.]+)/', $routeUri, $matches);
>>>>>>> develop2
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                if (!str_contains($matches[0][$i], ':')) {
                    continue;
                } else {
                    $this->arguments[$matches[1][$i]] = $requestUriSeg[$i];
                }
            }
            return true;
        } else {
            return false;
        }
    }
<<<<<<< HEAD
}
=======
}

>>>>>>> develop2
