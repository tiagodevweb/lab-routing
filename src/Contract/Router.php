<?php

declare(strict_types=1);

namespace Tdw\Routing\Contract;

use Psr\Http\Message\ServerRequestInterface;

interface Router extends \Countable
{
    /**
     * @param Route $route
     * @return Router
     */
    public function addGET(Route $route): Router;

    /**
     * @param Route $route
     * @return Router
     */
    public function addPOST(Route $route): Router;

    /**
     * @param string|null $method
     * @return array
     */
    public function all(string $method = null): array;

    /**
     * @param ServerRequestInterface $request
     * @return Route
     * @throws \InvalidArgumentException
     */
    public function match(ServerRequestInterface $request): Route;

    /**
     * @param string $name
     * @param array $parameters
     * @return string
     * @throws \InvalidArgumentException
     */
    public function generateUri(string $name, array $parameters): string;
}
