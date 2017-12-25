<?php

declare(strict_types=1);

namespace Tdw\Routing\Contract;

use Psr\Http\Message\UriInterface;

interface Route
{
    /**
     * @param UriInterface $uri
     * @return bool
     */
    public function parseUri(UriInterface $uri): bool;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return mixed
     */
    public function getCallback();

    /**
     * @return array
     */
    public function getParameters(): array;

    /**
     * @param string $key
     * @param Rule $rule
     * @return Route
     */
    public function addRule(string $key, Rule $rule): Route;

    /**
     * @param array $parameters
     * @return string
     */
    public function getUri(array $parameters): string;
}
