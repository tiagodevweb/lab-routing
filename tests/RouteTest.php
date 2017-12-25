<?php

namespace Tests\Routing;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tdw\Routing\Route;
use Tdw\Routing\Contract\Route as RouteInterface;
use Tdw\Routing\Rule\Id;
use Tdw\Routing\Rule\Slug;

class RouteTest extends TestCase
{
    /**
     * @group unitary
     */
    public function testInstanceOf()
    {
        //arrange
        $route = new Route('/articles/show/{slug}/{id}', function () {
        }, 'articles.show');

        //act
        $expected = RouteInterface::class;
        $actual = $route;

        //assert
        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * @group unitary
     */
    public function testShouldReturnFullStateWithClosure()
    {
        //arrange
        $callable = function () {
        };
        $name = 'articles.show';
        $route = new Route('/articles/show/{slug}/{id}', $callable, $name);
        $returnSlug = $route->addRule('slug', new Slug());
        $returnId = $route->addRule('id', new Id());

        //act
        $expectedName = $name;
        $actualName = $route->getName();
        $expectedCallback = $callable;
        $actualCallback = $route->getCallback();
        $expectedRules = ['slug' => '[0-9a-z\-]+', 'id' => '[0-9]+'];
        $actualRules = $route->getRules();

        //assert
        $this->assertEquals($expectedName, $actualName);
        $this->assertEquals($expectedCallback, $actualCallback);
        $this->assertEquals($expectedRules, $actualRules);
        $this->assertInstanceOf(Route::class, $returnSlug);
        $this->assertInstanceOf(Route::class, $returnId);
    }

    /**
     * @group unitary
     */
    public function testShouldReturnFullStateWithAction()
    {
        //arrange
        $action = '\App\ArticlesAction@show';
        $name = 'articles.show';
        $route = (new Route('/articles/show/{slug}/{id}', $action, $name))
            ->addRule('slug', new Slug())
            ->addRule('id', new Id());

        //act
        $expectedName = $name;
        $actualName = $route->getName();
        $expectedCallback = $action;
        $actualCallback = $route->getCallback();
        $expectedRules = ['slug' => '[0-9a-z\-]+', 'id' => '[0-9]+'];
        $actualRules = $route->getRules();

        //assert
        $this->assertEquals($expectedName, $actualName);
        $this->assertEquals($expectedCallback, $actualCallback);
        $this->assertEquals($expectedRules, $actualRules);
    }

    /**
     * @group unitary
     */
    public function testMatchShouldReturnTrue()
    {
        //arrange
        $request = new ServerRequest('GET', '/articles');
        $name = 'articles.show';
        $route = (new Route('/articles', '\App\ArticlesAction@index', $name));

        //act
        $expected = $route->parseUri($request->getUri());
        $expectedParameters = [];
        $actualParameters = $route->getParameters();

        //assert
        $this->assertTrue($expected);
        $this->assertEquals($expectedParameters, $actualParameters);
    }

    /**
     * @group unitary
     */
    public function testMatchShouldReturnFalse()
    {
        //arrange
        $request = new ServerRequest('GET', '/posts');
        $name = 'articles.show';
        $route = (new Route('/articles', '\App\ArticlesAction@index', $name));

        //act
        $expected = $route->parseUri($request->getUri());
        $expectedParameters = [];
        $actualParameters = $route->getParameters();

        //assert
        $this->assertFalse($expected);
        $this->assertEquals($expectedParameters, $actualParameters);
    }

    /**
     * @group unitary
     */
    public function testMatchShouldReturnTrueWithRule()
    {
        //arrange
        $request = new ServerRequest('GET', '/articles/show/title-post/25');
        $name = 'articles.show';
        $route = (new Route('/articles/show/{slug}/{id}', '\App\ArticlesAction@show', $name))
            ->addRule('slug', new Slug())
            ->addRule('id', new Id());

        //act
        $expected = $route->parseUri($request->getUri());
        $expectedParameters = ['slug' => 'title-post','id' => '25'];
        $actualParameters = $route->getParameters();

        //assert
        $this->assertTrue($expected);
        $this->assertEquals($expectedParameters, $actualParameters);
    }

    /**
     * @group unitary
     */
    public function testMatchShouldReturnFalseWithRule()
    {
        //arrange
        $request = new ServerRequest('GET', '/articles/show/title-post');
        $name = 'articles.show';
        $route = (new Route('/articles/show/{slug}/{id}', '\App\ArticlesAction@show', $name))
            ->addRule('slug', new Slug())
            ->addRule('id', new Id());

        //act
        $expected = $route->parseUri($request->getUri());
        $expectedParameters = [];
        $actualParameters = $route->getParameters();

        //assert
        $this->assertFalse($expected);
        $this->assertEquals($expectedParameters, $actualParameters);
    }

    /**
     * @group unitary
     */
    public function testGetUrl()
    {
        //arrange
        $route = (new Route('/articles', function () {
        }, 'articles.index'));

        //act
        $expected = '/articles';
        $actual = $route->getUri([]);

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group unitary
     */
    public function testGetUrlWithParameters()
    {
        //arrange
        $route = (new Route('/articles/{slug}/{id}', function () {
        }, 'articles.single'));

        //act
        $expected = '/articles/title-post/100';
        $actual = $route->getUri(['slug'=>'title-post','id'=>100]);

        //assert
        $this->assertEquals($expected, $actual);
    }
}
