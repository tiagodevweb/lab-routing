# Routing

Simple router to GET and POST requests.

[![Build Status](https://travis-ci.org/tiagodevweb/routing.svg?branch=master)](https://travis-ci.org/tiagodevweb/routing)

## Requirements

PHP: >=7.1

## Install

```bash
$ composer require tdw/routing
```

## Usage

`Instance`
```php
<?php

require 'vendor/autoload.php';

$routes = new \Tdw\Routing\Routes();
```

`Closure`
```php
<?php

$routes->addGET(new \Tdw\Routing\Route('/', function (){
    echo 'Home page';
}));
```

`Action and route name`
```php
<?php

$routes->addGET(new \Tdw\Routing\Route('/', 'App\ArticleAction@index', 'article.index'));
$routes->addPOST(new \Tdw\Routing\Route('/', 'App\ArticleAction@save', 'article.save'));
```

`Rule`
```php
<?php

$route = new \Tdw\Routing\Route('/posts/{slug}/{id}', function (){
  //
});
$route->addRule('slug', new \Tdw\Routing\Rule\Slug());
$route->addRule('id', new \Tdw\Routing\Rule\Id());
$routes->addGET($route);
```

`Match`
```php
<?php

$currentRoute = $routes->matchCurrent(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

var_dump($currentRoute);
```

## Exceptions

```
\Tdw\Routing\Exception\RouteNotFoundException
\Tdw\Routing\Exception\RouteNameNotFoundException
```

## Integration suggestion

```php
<?php

class App
{
    /**
     * @var \Tdw\Routing\Contract\Routes
     */
    private $routes;

    function __construct(\Tdw\Routing\Contract\Routes $routes)
    {
        $this->routes = $routes;
    }

    function run(\Psr\Http\Message\ServerRequestInterface $request)
    {
        if ($route = $this->routes->matchCurrent($request)) {
            if ($route->getCallback() instanceof Closure) {
                return call_user_func_array($route->getCallback(), $route->getParameters());
            }
            list( $action, $method ) = explode('@', $route->getCallback());
            return call_user_func_array([new $action, $method], $route->getParameters());

        }
        return new \GuzzleHttp\Psr7\Response(404, [], 'Page Not Found');
    }
}

(new App($routes))->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

```