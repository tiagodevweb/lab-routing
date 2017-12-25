# Routing

Simple router to GET and POST requests.

[![Build Status](https://travis-ci.org/tiagodevweb/routing.svg?branch=master)](https://travis-ci.org/tiagodevweb/routing)

## Requirements

PHP: >=7.0

## Install

```bash
$ composer require tdw/routing

require 'to/path/vendor/autoload.php';

```

## Usage

`Instance`
```php
<?php

$router = new \Tdw\Routing\Router();
```

`Closure`
```php
<?php

$router->addGET(new \Tdw\Routing\Route('/', function (){
    echo 'Home page';
}, 'home'));
```

`Action and route name`
```php
<?php

class ArticleAction
{
    /**
     * Route GET
     */
    public function index()
    {
        //
    }

    /**
     * Route POST
     */
    public function save()
    {
        //
    }
}

$router->addGET(new \Tdw\Routing\Route('/articles', 'ArticleAction@index', 'article.index'));
$router->addPOST(new \Tdw\Routing\Route('/articles/save', 'ArticleAction@save', 'article.save'));
```

`Rule`
```php
<?php

$route = (new \Tdw\Routing\Route('/article/{slug}/{id}', function () {
    //
}, 'articles.show'))
    ->addRule('slug', new \Tdw\Routing\Rule\Slug())
    ->addRule('id', new \Tdw\Routing\Rule\Id());
$router->addGET($route);
```

`Match`
```php
<?php

$currentRoute = $router->match(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

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
     * @var \Tdw\Routing\Contract\Router
     */
    private $router;

    function __construct(\Tdw\Routing\Contract\Router $router)
    {
        $this->router = $router;
    }

    function run(\Psr\Http\Message\ServerRequestInterface $request)
    {
        if ($route = $this->router->match($request)) {
            if ($route->getCallback() instanceof Closure) {
                return call_user_func_array($route->getCallback(), $route->getParameters());
            }
            list( $action, $method ) = explode('@', $route->getCallback());
            return call_user_func_array([new $action, $method], $route->getParameters());

        }
        return new \GuzzleHttp\Psr7\Response(404, [], 'Page Not Found');
    }
}

(new App($router))->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

```