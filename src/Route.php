<?php

declare(strict_types=1);

namespace Tdw\Routing;

use Psr\Http\Message\UriInterface;
use Tdw\Routing\Contract\Route as IRoute;
use Tdw\Routing\Contract\Rule;
use Tdw\Routing\Rule\Url;
use Tdw\Routing\Rule\Id;
use Tdw\Routing\Rule\Slug;

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
        if ($this->hasParameter($this->path)) {
            $parameters = array_filter(explode('/', $this->path));
            foreach ($parameters as $parameter) {
                if ($this->isParameter($parameter)) {
                    $key = $this->clearParameter($parameter);
                    switch ($key) {
                        case 'id':
                            $this->addRule($key, new Id());
                            break;
                        case 'slug':
                            $this->addRule($key, new Slug());
                            break;
                        default:
                            $this->addRule($key, new Url());
                            break;
                    }
                    
                }
            }
        }
    }

    private function hasParameter(string $path): bool
    {
        return strpos($path, '{') and strpos($path, '}');
    }

    private function isParameter(string $string): bool
    {
        return '{' === $string[0] and '}' === substr($string, -1);
    }

    private function clearParameter(string $parameter)
    {
        return str_replace(['{','}'], '', $parameter);
    }
}
