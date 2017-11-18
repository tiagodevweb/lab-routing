<?php

namespace Tests\Routing;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tdw\Routing\Contract\Routes as RoutesInterface;
use Tdw\Routing\Method\GET;
use Tdw\Routing\Method\POST;
use Tdw\Routing\Route;
use Tdw\Routing\Routes;
use Tdw\Routing\Rule\Url;

class RoutesTest extends TestCase
{
    /**
     * @group unitary
     */
    public function testInstanceOf()
    {
        //arrange
        $routes = new Routes();

        //act
        $expected = RoutesInterface::class;
        $actual = $routes;

        //assert
        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * @group unitary
     */
    public function testShouldReturnFullStateEmpty()
    {
        //arrange
        $routes = new Routes();

        //act
        $expectedCount = 0;

        //assert
        $this->assertCount($expectedCount, $routes);
    }

    /**
     * @group unitary
     */
    public function testShouldReturnFullStateWithAddRoute()
    {
        //arrange
        $routes = new Routes();
        $routes->addGET($this->createMock(Route::class))
               ->addPOST($this->createMock(Route::class));

        //act
        $expectedCount = 2;

        //assert
        $this->assertCount($expectedCount, $routes);
    }

    /**
     * @group integration
     */
    public function testShouldReturnMatchCurrentRouteGet()
    {
        //arrange
        $request = new ServerRequest(new GET(), '/articles');
        $routes = new Routes();
        $route = new Route('/articles', 'App\ArticlesAction@index', 'articles.index');
        $routes->addGET($route);

        //act
        $expected = $route;
        $actual = $routes->matchCurrent($request);

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration
     */
    public function testShouldReturnMatchCurrentRouteGetWithParameters()
    {
        //arrange
        $request = new ServerRequest(new GET(), '/articles/show/25');
        $routes = new Routes();
        $route = new Route('/articles/show/{id}', 'App\ArticlesAction@show', 'articles.single');
        $routes->addGET($route);

        //act
        $expected = $route;
        $actual = $routes->matchCurrent($request);

        //assert
        $this->assertEquals($expected, $actual);
        $this->assertEquals(['id' => 25], $route->getParameters());
        $this->assertEquals(['id' => new Url()], $route->getRules());
    }

    /**
     * @group integration
     */
    public function testShouldReturnMatchCurrentRoutePost()
    {
        //arrange
        $request = new ServerRequest(new POST(), '/articles/create');
        $routes = new Routes();
        $route = new Route('/articles/create', 'App\ArticlesAction@create', 'articles.create');
        $routes->addPOST($route);

        //act
        $expected = $route;
        $actual = $routes->matchCurrent($request);

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration
     */
    public function testShouldReturnUrlCreated()
    {
        //arrange
        $routes = new Routes();
        $route = new Route('/articles/show/{slug}/{id}', 'App\ArticlesAction@create', 'articles.show');
        $routes->addPOST($route);

        //act
        $expected = '/articles/show/title-post/100';
        $actual = $routes->generateUrl('articles.show', ['slug' => 'title-post', 'id' => 100]);

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration
     */
    public function testShouldReturnAllRoutes()
    {
        //arrange
        $request = new ServerRequest(new GET(), '/articles/create');
        $routes = new Routes();
        $routeCreate = new Route('/articles/create', 'App\ArticlesAction@create', 'articles.create');
        $routeSave = new Route('/articles/save', 'App\ArticlesAction@save', 'articles.save');
        $routeEdit = new Route('/articles/update', 'App\ArticlesAction@edit', 'articles.edit');
        $routeUpdate = new Route('/articles/update', 'App\ArticlesAction@update', 'articles.update');
        $routes->addGET($routeCreate)
               ->addPOST($routeSave)
               ->addGET($routeEdit)
               ->addPOST($routeUpdate);

        //act
        $expected = [
            'GET' => [
                $routeCreate, $routeEdit
            ],
            'POST' => [
                $routeSave, $routeUpdate
            ]
        ];
        $actual = $routes->all();

        //assert
        $this->assertEquals($expected, $actual);
        $this->assertCount(2, $routes);
        $this->assertEquals(2, count($routes->all('GET')));
        $this->assertEquals(2, count($routes->all('POST')));
        $this->assertEquals($routeCreate, $routes->matchCurrent($request));
    }

    /**
     * @group integration
     * @expectedException \Tdw\Routing\Exception\RouteNotFoundException
     * @expectedExceptionMessage No matching routes
     */
    public function testShouldReturnRouteNotFoundException()
    {
        //arrange
        $request = new ServerRequest(new GET(), '/articles/create');
        $routes = new Routes();
        $route = new Route('/not-found', function () {
        }, 'articles.create');
        $routes->addGET($route);

        //act
        $routes->matchCurrent($request);
        //assert
    }

    /**
     * @group integration
     * @expectedException \Tdw\Routing\Exception\RouteNameNotFoundException
     * @expectedExceptionMessage No route matches this name
     */
    public function testShouldReturnRouteNameNotFoundException()
    {
        //arrange
        $request = new ServerRequest(new GET(), '/articles/create');
        $routes = new Routes();
        $route = new Route('/not-found', function () {
        }, 'articles.create');
        $routes->addGET($route);

        //act
        $routes->generateUrl('route-name', []);

        //assert
    }
}
