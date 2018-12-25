<?php

declare(strict_types=1);

namespace Tdw\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Tdw\Routing\Contract\Route as IRoute;
use Tdw\Routing\Contract\Router as IRouter;
use Tdw\Routing\Exception\RouteNameNotFoundException;
use Tdw\Routing\Exception\RouteNotFoundException;

class Router implements IRouter
{

    /**
     * @var IRoute[]
     */
    private $routes = [];

    public function addGET(IRoute $route): IRouter
    {
        return $this->add($route, 'GET');
    }

    public function addPOST(IRoute $route): IRouter
    {
        return $this->add($route, 'POST');
    }

    public function addPUT(IRoute $route): IRouter
    {
        return $this->add($route, 'PUT');
    }

    public function addPATCH(IRoute $route): IRouter
    {
        return $this->add($route, 'PATCH');
    }

    public function addDELETE(IRoute $route): IRouter
    {
        return $this->add($route, 'DELETE');
    }

    /**
     * @inheritdoc
     */
    public function generateUri(string $name, array $parameters): string
    {
        /**@var IRoute $route*/
        foreach ($this->routes as $routes) {
            foreach ($routes as $route) {
                if ($name === $route->getName()) {
                    return $route->getUri($parameters);
                }
            }
        }
        throw new RouteNameNotFoundException();
    }

    /**
     * @inheritdoc
     */
    public function match(ServerRequestInterface $request): IRoute
    {
        /** @var Route $route */
        foreach ($this->routes[$request->getMethod()] as $route) {
            if ($route->parseUri($request->getUri())) {
                return $route;
            }
        }
        throw new RouteNotFoundException();
    }

    /**
     * @inheritdoc
     */
    public function all(string $method = null): array
    {
        if ($method) {
            $arr = [];
            foreach ($this->routes as $key => $routes) {
                if ($key === $method) {
                    $arr[] = $routes;
                }
            };
            return $arr[0];
        }
        return $this->routes;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->routes);
    }

    /**
     * @param IRoute $route
     * @param string $method
     * @return IRouter
     */
    private function add(IRoute $route, string $method): IRouter
    {
        $this->routes[(string)$method][] = $route;
        return $this;
    }
}
