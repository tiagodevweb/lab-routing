<?php

namespace Tests\Routing;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tdw\Routing\Contract\Router as RouterInterface;
use Tdw\Routing\Exception\RouteNameNotFoundException;
use Tdw\Routing\Exception\RouteNotFoundException;
use Tdw\Routing\Route;
use Tdw\Routing\Router;
use Tdw\Routing\Rule\Id;

class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    protected $router;

    public function setUp()
    {
        $this->router = new Router();
    }

    /**
     * @group unitary
     */
    public function testInstanceOf()
    {
        //arrange

        //act
        $expected = RouterInterface::class;
        $actual = $this->router;

        //assert
        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * @group unitary
     */
    public function testShouldReturnFullStateEmpty()
    {
        //arrange

        //act
        $expectedCount = 0;

        //assert
        $this->assertCount($expectedCount, $this->router);
    }

    /**
     * @group unitary
     */
    public function testShouldReturnFullStateWithAddRoute()
    {
        //arrange
        $this->router->addGET($this->createMock(Route::class))
               ->addPOST($this->createMock(Route::class));

        //act
        $expectedCount = 2;

        //assert
        $this->assertCount($expectedCount, $this->router);
    }

    /**
     * @group integration
     */
    public function testShouldReturnMatchCurrentRouteGet()
    {
        //arrange
        $request = new ServerRequest('GET', '/articles');
        $route = new Route('/articles', 'App\ArticlesAction@index', 'articles.index');
        $this->router->addGET($route);

        //act
        $expected = $route;
        $actual = $this->router->match($request);

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration
     */
    public function testShouldReturnMatchCurrentRouteGetWithParameters()
    {
        //arrange
        $request = new ServerRequest('GET', '/articles/show/25');
        $route = new Route('/articles/show/{id}', 'App\ArticlesAction@show', 'articles.single');
        $this->router->addGET($route);

        //act
        $expected = $route;
        $actual = $this->router->match($request);

        //assert
        $this->assertEquals($expected, $actual);
        $this->assertEquals(['id' => 25], $route->getParameters());
        $this->assertEquals(['id' => (new Id())->asRegex()], $route->getRules());
    }

    /**
     * @group integration
     */
    public function testShouldReturnMatchCurrentRoutePost()
    {
        //arrange
        $request = new ServerRequest('POST', '/articles/create');
        $route = new Route('/articles/create', 'App\ArticlesAction@create', 'articles.create');
        $this->router->addPOST($route);

        //act
        $expected = $route;
        $actual = $this->router->match($request);

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration
     */
    public function testShouldReturnMatchCurrentRoutePut()
    {
        //arrange
        $request = new ServerRequest('PUT', '/articles/upate/1');
        $route = new Route('/articles/upate/{id}', 'App\ArticlesAction@upate', 'articles.upate');
        $this->router->addPUT($route);

        //act
        $expected = $route;
        $actual = $this->router->match($request);

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration
     */
    public function testShouldReturnMatchCurrentRoutePatch()
    {
        //arrange
        $request = new ServerRequest('PATCH', '/articles/upate/1');
        $route = new Route('/articles/upate/{id}', 'App\ArticlesAction@upate', 'articles.upate');
        $this->router->addPATCH($route);

        //act
        $expected = $route;
        $actual = $this->router->match($request);

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration
     */
    public function testShouldReturnMatchCurrentRouteDelete()
    {
        //arrange
        $request = new ServerRequest('DELETE', '/articles/delete/1');
        $route = new Route('/articles/delete/{id}', 'App\ArticlesAction@delete', 'articles.delete');
        $this->router->addDELETE($route);

        //act
        $expected = $route;
        $actual = $this->router->match($request);

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration
     */
    public function testShouldReturnUrlCreated()
    {
        //arrange
        $route = new Route('/articles/show/{slug}/{id}', 'App\ArticlesAction@create', 'articles.show');
        $this->router->addPOST($route);

        //act
        $expected = '/articles/show/title-post/100';
        $actual = $this->router->generateUri('articles.show', ['slug' => 'title-post', 'id' => 100]);

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration
     */
    public function testShouldReturnAllRoutes()
    {
        //arrange
        $request = new ServerRequest('GET', '/articles/create');
        $routeCreate = new Route('/articles/create', 'App\ArticlesAction@create', 'articles.create');
        $routeSave = new Route('/articles/save', 'App\ArticlesAction@save', 'articles.save');
        $routeEdit = new Route('/articles/update', 'App\ArticlesAction@edit', 'articles.edit');
        $routeUpdate = new Route('/articles/update', 'App\ArticlesAction@update', 'articles.update');
        $this->router->addGET($routeCreate)
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
        $actual = $this->router->all();

        //assert
        $this->assertEquals($expected, $actual);
        $this->assertCount(2, $this->router);
        $this->assertEquals(2, count($this->router->all('GET')));
        $this->assertEquals(2, count($this->router->all('POST')));
        $this->assertEquals($routeCreate, $this->router->match($request));
    }

    /**
     * @group integration
     */
    public function testShouldReturnRouteNotFoundException()
    {
        //arrange
        $request = new ServerRequest('GET', '/articles/create');
        $route = new Route('/not-found', function () {
        }, 'articles.create');
        $this->router->addGET($route);

        //act
        $this->expectException(RouteNotFoundException::class);
        $this->expectExceptionMessage('No matching route');
        $this->router->match($request);
        //assert
    }

    /**
     * @group integration
     */
    public function testShouldReturnRouteNameNotFoundException()
    {
        //arrange
        $request = new ServerRequest('GET', '/articles/create');
        $route = new Route('/not-found', function () {
        }, 'articles.create');
        $this->router->addGET($route);

        //act
        $this->expectException(RouteNameNotFoundException::class);
        $this->expectExceptionMessage('No route matches this name');
        $this->router->generateUri('route-name', []);

        //assert
    }
}
