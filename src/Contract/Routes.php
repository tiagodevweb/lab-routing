<?php

declare(strict_types=1);

namespace Tdw\Routing\Contract;

use Psr\Http\Message\ServerRequestInterface;

interface Routes extends \Countable
{
    public function addGET(Route $route): Routes;
    public function addPOST(Route $route): Routes;
    public function all(?string $method = null): array;
    /**
     * @param ServerRequestInterface $request
     * @return Route
     * @throws \InvalidArgumentException
     */
    public function matchCurrent(ServerRequestInterface $request): Route;

    /**
     * @param string $routeName
     * @param array $parameters
     * @return string
     * @throws \InvalidArgumentException
     */
    public function generateUrl(string $routeName, array $parameters): string;
}
