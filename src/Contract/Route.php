<?php

declare(strict_types=1);

namespace Tdw\Routing\Contract;

use Psr\Http\Message\UriInterface;

interface Route
{
    public function match(UriInterface $uri): bool;
    public function getName(): ?string;
    public function getCallback();
    public function getParameters(): array;
    public function addRule(string $key, Rule $rule): Route;
    public function getRules(): array;
    public function getUrl(array $parameters): string;
}
