<?php

declare(strict_types=1);

namespace Tdw\Routing;

use Psr\Http\Message\UriInterface;
use Tdw\Routing\Contract\Route as IRoute;
use Tdw\Routing\Contract\Rule;
use Tdw\Routing\Rule\Url;

class Route implements IRoute
{
    private $path;
    private $callback;
    private $name;
    private $rules = [];
    private $parameters = [];

    public function __construct(string $path, $callback, string $name)
    {
        $this->path = $path;
        $this->callback = $callback;
        $this->name = $name;
        $this->ruleDefault();
    }

    public function addRule(string $key, Rule $rule): IRoute
    {
        $this->rules[$key] = $rule->asRegex();
        return $this;
    }

    public function parseUri(UriInterface $uri): bool
    {
        if (preg_match($this->regex(), trim($uri->getPath(), '/'), $matches)) {
            array_shift($matches);
            $this->parameters = array_unique($matches);
            return true;
        }
        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getUri(array $parameters): string
    {
        $url = $this->path;
        foreach ($parameters as $k => $v) {
            $url = str_replace("{{$k}}", $v, $url);
        }
        return $url;
    }

    private function regex(): string
    {
        return '#^'.preg_replace_callback('#{([\w]+)}#', [$this, 'paramMatch'], trim($this->path, '/')).'$#';
    }

    private function paramMatch($match)
    {
        if (isset($this->rules[$match[1]])) {
            return '(?P<' . $match[1] . '>' . $this->rules[$match[1]] . ')';
        }
        return '([^/]+)';
    }

    private function ruleDefault()
    {
        if (strpos($this->path, '{') and strpos($this->path, '}')) {
            $parameters = array_filter(explode('/', $this->path));
            foreach ($parameters as $parameter) {
                if ('{' === $parameter[0] and '}' === substr($parameter, -1)) {
                    $this->addRule(str_replace(['{','}'], '', $parameter), new Url());
                }
            }
        }
    }
}
