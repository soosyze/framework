<?php

namespace Soosyze\Test;

use \Soosyze\Components\Http\Request;

class RoutingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Routing
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $routes = [
            "test.index" => [
                "path" => "/",
                "uses" => "\Soosyze\Test\TestController@index"
            ],
            "test.page"  => [
                "path" => "page/:item",
                "uses" => "TestController@page",
                "with" => [
                    ":item" => "[0-9]+"
                ],
            ]
        ];

        $this->object = new \Soosyze\Router($routes);
        $this->object->setObjects([ 'TestController' => new TestController() ]);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    public function testParse()
    {
        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = [
            'path' => "/",
            "uses" => "\Soosyze\Test\TestController@index",
            'key'  => "test.index"
        ];

        $this->assertEquals($route, $result);

        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com/?q=404');
        $request = new Request('GET', $uri);

        $route = $this->object->parse($request);
        $this->assertNull($route);
    }

    public function testExecute()
    {
        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route, $request);

        $this->assertEquals($result, "hello world !");
    }

    public function testExecuteParam()
    {
        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com/?page/1');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route, $request);

        $this->assertEquals($result, "hello page 1");
    }

    /**
     * @expectedException Exception
     */
    public function testExecuteExceptionNotRequest()
    {
        $route = [
            'path' => "page/:item",
            'uses' => "TestController@page",
            'with' => [
                ':item' => "[0-9]+"
            ],
            'key'  => "test.page"
        ];
        $this->object->execute($route);
    }

    public function testRelplaceSlash()
    {
        $str    = "/index/page/1/edit";
        $result = $this->object->relplaceSlash($str);

        $this->assertEquals($result, "%2Findex%2Fpage%2F1%2Fedit");
    }

    public function testGetRoute()
    {
        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com/?test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request);
        $result = $this->object->getRoute('test.index');

        $this->assertEquals($result, "http://test.com/?/");
    }

    public function testGetRouteParam()
    {
        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com/?test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request);
        $result = $this->object->getRoute('test.page', [ ':item' => '1' ]);

        $this->assertEquals($result, "http://test.com/?page/1");
    }

    /**
     * @expectedException Exception
     */
    public function testGetRouteException()
    {
        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com/');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request);
        $this->object->getRoute('error');
    }

    /**
     * @expectedException Exception
     */
    public function testGetRouteRouteArgumentException()
    {
        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com/');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request);
        $this->object->getRoute('test.page', [ ':item' => 'error' ]);
    }

    public function testIsRewrite()
    {
        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com/');
        $request = new Request('GET', $uri, [ 'HTTP_MOD_REWRITE' => 'On' ]);

        $this->object->setRequest($request)
            ->setSettings([ 'RewriteEngine' => 'on' ]);

        $this->assertTrue($this->object->isRewrite());

        $result = $this->object->getRoute('test.page', [ ':item' => '1' ]);
        $this->assertEquals($result, "http://test.com/page/1");
    }
}

class TestController
{

    public function index()
    {
        return "hello world !";
    }

    public function page( $item )
    {
        return "hello page " . $item;
    }
}

class TestControllerOther
{

    public function index()
    {
        return "hello world !";
    }
}