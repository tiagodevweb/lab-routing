# Routing

Simple router to GET and POST requests.

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
    //
}));
```

`Action`
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