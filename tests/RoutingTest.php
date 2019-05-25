<?php

namespace Soosyze\Test;

use Soosyze\Components\Http\Request;
use Soosyze\Components\Http\Uri;
use Soosyze\Router;

class RoutingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Router
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $routes = [
            'test.index' => [
                'methode' => 'GET',
                'path' => '/',
                'uses' => "\Soosyze\Test\TestController@index"
            ],
            'test.post' => [
                'methode' => 'POST',
                'path' => '/',
                'uses' => "\Soosyze\Test\TestController@indexPost"
            ],
            'test.page'  => [
                'methode' => 'GET',
                'path' => 'page/:item',
                'uses' => 'TestController@page',
                'with' => [
                    ':item' => '[0-9]+'
                ],
            ]
        ];

        $this->object = new Router($routes);
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
        $uri     = Uri::create('http://test.com');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = [
            'methode' => 'GET',
            'path' => '/',
            'uses' => "\Soosyze\Test\TestController@index",
            'key'  => 'test.index'
        ];

        $this->assertEquals($route, $result);

        $uri     = Uri::create('http://test.com/?q=404');
        $request = new Request('GET', $uri);

        $route = $this->object->parse($request);
        $this->assertNull($route);
    }
    
    public function testExecute()
    {
        $uri     = Uri::create('http://test.com');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route, $request);

        $this->assertEquals($result, 'hello world !');
    }
    
    public function testExecuteSetRequest()
    {
        $uri     = Uri::create('http://test.com');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->setRequest($request)->execute($route);

        $this->assertEquals($result, 'hello world !');
    }

    public function testExecuteParam()
    {
        $uri     = Uri::create('http://test.com/?page/1');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route, $request);

        $this->assertEquals($result, 'hello page 1');
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteExceptionNotRequest()
    {
        $uri     = Uri::create('http://test.com/?page/1');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $this->object->execute($route);
    }

    public function testRelplaceSlash()
    {
        $str    = '/index/page/1/edit';
        $result = $this->object->relplaceSlash($str);

        $this->assertEquals($result, '%2Findex%2Fpage%2F1%2Fedit');
    }

    public function testGetRoute()
    {
        $uri     = Uri::create('http://test.com/?test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request)->setBasePath('http://test.com/');
        $result = $this->object->getRoute('test.index');

        $this->assertEquals($result, 'http://test.com/?/');
    }

    public function testGetRouteParam()
    {
        $uri     = Uri::create('http://test.com/?test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request)->setBasePath('http://test.com/');
        $result = $this->object->getRoute('test.page', [ ':item' => '1' ]);

        $this->assertEquals($result, 'http://test.com/?page/1');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetRouteException()
    {
        $uri     = Uri::create('http://test.com/');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request);
        $this->object->getRoute('error');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetRouteRouteArgumentException()
    {
        $uri     = Uri::create('http://test.com/');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request);
        $this->object->getRoute('test.page', [ ':item' => 'error' ]);
    }

    public function testIsRewrite()
    {
        $uri     = Uri::create('http://test.com/');
        $request = new Request('GET', $uri, [ 'HTTP_MOD_REWRITE' => 'On' ]);

        $this->object->setRequest($request)
            ->setBasePath('http://test.com/')
            ->setConfig([ 'settings.rewrite_engine' => 'on' ]);

        $this->assertTrue($this->object->isRewrite());

        $result = $this->object->getRoute('test.page', [ ':item' => '1' ]);
        $this->assertEquals($result, 'http://test.com/page/1');
    }
    
    public function testGetBasePath()
    {
        $uri     = Uri::create('http://test.com/');
        $request = new Request('GET', $uri, [ 'HTTP_MOD_REWRITE' => 'On' ]);

        $this->object->setRequest($request);
        $this->assertEquals($this->object->getBasePath(), '');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetConfigInvalidArgumentException()
    {
        $this->object->setConfig('error');
    }
}

class TestController
{
    public function index()
    {
        return 'hello world !';
    }

    public function page($item)
    {
        return 'hello page ' . $item;
    }
}

class TestControllerOther
{
    public function index()
    {
        return 'hello world !';
    }
}
