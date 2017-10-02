<?php

declare(strict_types=1);

namespace Tdw\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Tdw\Routing\Contract\Method;
use Tdw\Routing\Contract\Route as IRoute;
use Tdw\Routing\Contract\Routes as IRoutes;
use Tdw\Routing\Exception\RouteNameNotFoundException;
use Tdw\Routing\Exception\RouteNotFoundException;
use Tdw\Routing\Method\GET;
use Tdw\Routing\Method\POST;

class Routes implements IRoutes
{

    /**
     * @var IRoute[]
     */
    private $routes = [];

    public function addGET(IRoute $route): IRoutes
    {
        return $this->add($route, new GET());
    }

    public function addPOST(IRoute $route): IRoutes
    {
        return $this->add($route, new POST());
    }

    /**
     * @inheritdoc
     */
    public function generateUrl(string $routeName, array $parameters): string
    {
        /**@var IRoute $route*/
        foreach ($this->routes as $routes) {
            foreach ($routes as $route) {
                if ($routeName === $route->getName()) {
                    return $route->getUrl($parameters);
                }
            }
        }
        throw new RouteNameNotFoundException();
    }

    /**
     * @inheritdoc
     */
    public function matchCurrent(ServerRequestInterface $request): IRoute
    {
        /** @var Route $route */
        foreach ($this->routes[$request->getMethod()] as $route) {
            if ($route->match($request->getUri())) {
                return $route;
            }
        }
        throw new RouteNotFoundException();
    }

    public function all(?string $method = null): array
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

    public function count()
    {
        return count($this->routes);
    }

    private function add(IRoute $route, Method $method): IRoutes
    {
        $this->routes[(string)$method][] = $route;
        return $this;
    }
}
