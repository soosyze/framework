<?php

namespace Soosyze\Tests\Components\Router;

use Soosyze\Components\Http\Request;
use Soosyze\Components\Http\Uri;
use Soosyze\Components\Router\Route;
use Soosyze\Components\Router\Router;

class RouterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Router
     */
    protected $object;

    public static function setUpBeforeClass()
    {
        Route::useNamespace('');
        Route::get('test.index', '/', 'Soosyze\Tests\Components\Router\TestController@index');
        Route::post('test.post', '/', 'Soosyze\Tests\Components\Router\TestController@indexPost');

        Route::useNamespace('Soosyze\Tests\Components\Router');
        Route::get('test.page', 'page/:id', 'TestController@page', [ ':id' => '[0-9]+' ]);
        Route::post('test.post', 'page/:id/post', 'TestController@post', [ ':id' => '[0-9]+' ]);
        Route::put('test.put', 'page/:id/put', 'TestController@put', [ ':id' => '[0-9]+' ]);
        Route::path('test.path', 'page/:id/path', 'TestController@path', [ ':id' => '[0-9]+' ]);
        Route::option('test.option', 'page/:id/option', 'TestController@option', [
            ':id' => '[0-9]+' ]);
        Route::delete('test.delete', 'page/:id/delete', 'TestController@delete', [
            ':id' => '[0-9]+' ]);
        Route::get('test.page.format', 'page/:id.:ext', 'TestController@format', [
            ':id'  => '[0-9]+',
            ':ext' => 'json|xml'
        ]);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Router();
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
            'key'     => 'test.index',
            'methode' => 'get',
            'path'    => '/',
            'uses'    => "\Soosyze\Tests\Components\Router\TestController@index",
            'with'    => []
        ];

        $this->assertEquals($route, $result);

        $uri     = Uri::create('http://test.com/?q=404');
        $request = new Request('GET', $uri);

        $route = $this->object->parse($request);
        $this->assertNull($route);
    }

    public function testParseQueryFromRequest()
    {
        $uri     = Uri::create('http://test.com');
        $request = new Request('GET', $uri);
        $out     = $this->object->setRequest($request)->parseQueryFromRequest();

        $this->assertEquals($out, '/');

        $uri     = Uri::create('http://test.com?q=foo');
        $request = new Request('GET', $uri);
        $out     = $this->object->setRequest($request)->parseQueryFromRequest();

        $this->assertEquals($out, 'foo');
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
        $uri     = Uri::create('http://test.com/?q=page/1');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route, $request);

        $this->assertEquals($result, 'hello page 1');

        $uri     = Uri::create('http://test.com/?q=page/1#foo');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route, $request);

        $this->assertEquals($result, 'hello page 1');
    }

    public function testExecuteParamMultiple()
    {
        $uri     = Uri::create('http://test.com/?q=page/1.json');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route, $request);
        $this->assertEquals($result, 'hello json 1');
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteExceptionNotRequest()
    {
        $uri     = Uri::create('http://test.com/?q=page/1');
        $request = new Request('GET', $uri);

        $route = $this->object->parse($request);
        $this->object->execute($route);
    }

    public function testRelplaceRegex()
    {
        $str = 'index/page/:id/edit';

        $result = $this->object->getRegexForPath($str, [ ':id' => '\d+' ]);
        $this->assertEquals($result, 'index\/page\/(\d+)\/edit');

        $result2 = $this->object->getRegexForPath($str, [ ':id' => '(\d+)' ]);
        $this->assertEquals($result2, 'index\/page\/((?:\d+))\/edit');
    }

    public function testGetRoute()
    {
        $uri     = Uri::create('http://test.com/?q=test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request)->setBasePath('http://test.com/');
        $result = $this->object->getRoute('test.index');

        $this->assertEquals($result, 'http://test.com/?q=/');
    }

    public function testGetRequestByRoute()
    {
        $uri     = Uri::create('http://test.com/?q=test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request)->setBasePath('http://test.com/');
        $result = $this->object->getRequestByRoute('test.index');

        $this->assertEquals((string) $result->getUri(), 'http://test.com/?q=/');

        /* Rewrite */
        $uriRewrite     = Uri::create('http://test.com/test');
        $requestRewrite = new Request('GET', $uriRewrite);

        $this->object
            ->setConfig([ 'settings.rewrite_engine' => true ])
            ->setBasePath('http://test.com/')
            ->setRequest($requestRewrite);
        $resultRewrite = $this->object->getRequestByRoute('test.index');

        $this->assertEquals((string) $resultRewrite->getUri(), 'http://test.com/');
    }

    public function testGetRouteStrictParam()
    {
        $uri     = Uri::create('http://test.com/?q=test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request)->setBasePath('http://test.com/');
        $result = $this->object->getRoute('test.page', [ ':id' => '1' ]);

        $this->assertEquals($result, 'http://test.com/?q=page/1');
    }

    public function testGetRouteParam()
    {
        $uri     = Uri::create('http://test.com/?q=test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request)->setBasePath('http://test.com/');
        $result = $this->object->getRoute('test.page.format', [ ':ext' => 'json' ], false);

        $this->assertEquals($result, 'http://test.com/?q=page/:id.json');
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
        $this->object->getRoute('test.page', [ ':id' => 'error' ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetRouteInvalidArgumentException()
    {
        $uri     = Uri::create('http://test.com/');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request);
        $this->object->getRoute('test.page', [ ':error' => 1 ]);
    }

    public function testIsRewrite()
    {
        $uri     = Uri::create('http://test.com/');
        $request = new Request('GET', $uri, [ 'HTTP_MOD_REWRITE' => 'On' ]);

        $this->object->setRequest($request)
            ->setBasePath('http://test.com/')
            ->setConfig([ 'settings.rewrite_engine' => 'on' ]);

        $this->assertTrue($this->object->isRewrite());

        $result = $this->object->getRoute('test.page', [ ':id' => '1' ]);
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

    public function testMakeRoute()
    {
        $url = $this->object
            ->setBasePath('http://test.com')
            ->makeRoute('foo/route');

        $this->assertEquals($url, 'http://test.com?q=foo/route');
    }
}

class TestController
{
    public function index()
    {
        return 'hello world !';
    }

    public function format($item, $ext)
    {
        return "hello $ext $item";
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
