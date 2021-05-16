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

    public static function setUpBeforeClass(): void
    {
    }

    protected function setUp(): void
    {
        $this->object = new Router();
        Route::useNamespace('');
        Route::get('test.index', '/', 'Soosyze\Tests\Resources\Router\TestController@index');
        Route::post('test.post', '/', 'Soosyze\Tests\Resources\Router\TestController@indexPost');

        Route::useNamespace('Soosyze\Tests\Resources\Router');
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

    public function testParse(): void
    {
        $uri     = Uri::create('http://test.com');
        $request = new Request('GET', $uri);

        $expectedRoute = [
            'key'     => 'test.index',
            'methode' => 'get',
            'path'    => '/',
            'uses'    => "\Soosyze\Tests\Resources\Router\TestController@index",
            'with'    => []
        ];

        $this->assertEquals($expectedRoute, $this->object->parse($request));

        $uri     = Uri::create('http://test.com/?q=404');
        $request = new Request('GET', $uri);

        $this->assertNull($this->object->parse($request));
    }

    public function testParseQueryFromRequest(): void
    {
        $uri     = Uri::create('http://test.com');
        $request = new Request('GET', $uri);
        $out     = $this->object->setRequest($request)->parseQueryFromRequest();

        $this->assertEquals('/', $out);

        $uri     = Uri::create('http://test.com?q=foo');
        $request = new Request('GET', $uri);
        $out     = $this->object->setRequest($request)->parseQueryFromRequest();

        $this->assertEquals('foo', $out);
    }

    public function testExecute(): void
    {
        $uri     = Uri::create('http://test.com');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route ?? [], $request);

        $this->assertEquals('hello world !', $result);
    }

    public function testExecuteSetRequest(): void
    {
        $uri     = Uri::create('http://test.com');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->setRequest($request)->execute($route ?? []);

        $this->assertEquals('hello world !', $result);
    }

    public function testExecuteParam(): void
    {
        $uri     = Uri::create('http://test.com/?q=page/1');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route ?? [], $request);

        $this->assertEquals('hello page 1', $result);

        $uri     = Uri::create('http://test.com/?q=page/1#foo');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route ?? [], $request);

        $this->assertEquals('hello page 1', $result);
    }

    public function testExecuteParamMultiple(): void
    {
        $uri     = Uri::create('http://test.com/?q=page/1.json');
        $request = new Request('GET', $uri);

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route ?? [], $request);
        $this->assertEquals('hello json 1', $result);
    }

    public function testExecuteExceptionNotRequest(): void
    {
        $uri     = Uri::create('http://test.com/?q=page/1');
        $request = new Request('GET', $uri);

        $route = $this->object->parse($request);

        $this->expectException(\Exception::class);
        $this->object->execute($route ?? []);
    }

    public function testRelplaceRegex(): void
    {
        $str = 'index/page/:id/edit';

        $this->assertEquals(
            'index\/page\/(\d+)\/edit',
            $this->object->getRegexForPath($str, [ ':id' => '\d+' ])
        );

        $this->assertEquals(
            'index\/page\/((?:\d+))\/edit',
            $this->object->getRegexForPath($str, [ ':id' => '(\d+)' ])
        );
    }

    public function testGetRoute(): void
    {
        $uri     = Uri::create('http://test.com/?q=test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request)->setBasePath('http://test.com/');

        $this->assertEquals(
            'http://test.com/?q=/',
            $this->object->getRoute('test.index')
        );
    }

    public function testGetRequestByRoute(): void
    {
        $uri     = Uri::create('http://test.com/?q=test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request)->setBasePath('http://test.com/');
        $result = $this->object->getRequestByRoute('test.index');

        $this->assertEquals('http://test.com/?q=/', (string) $result->getUri());

        /* Rewrite */
        $uriRewrite     = Uri::create('http://test.com/test');
        $requestRewrite = new Request('GET', $uriRewrite);

        $this->object
            ->setConfig([ 'settings.rewrite_engine' => true ])
            ->setBasePath('http://test.com/')
            ->setRequest($requestRewrite);
        $resultRewrite = $this->object->getRequestByRoute('test.index');

        $this->assertEquals('http://test.com/', (string) $resultRewrite->getUri());
    }

    public function testGetRouteStrictParam(): void
    {
        $uri     = Uri::create('http://test.com/?q=test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request)->setBasePath('http://test.com/');

        $this->assertEquals(
            'http://test.com/?q=page/1',
            $this->object->getRoute('test.page', [ ':id' => '1' ])
        );
    }

    public function testGetRouteParam(): void
    {
        $uri     = Uri::create('http://test.com/?q=test');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request)->setBasePath('http://test.com/');

        $this->assertEquals(
            'http://test.com/?q=page/:id.json',
            $this->object->getRoute('test.page.format', [ ':ext' => 'json' ], false)
        );
    }

    /**
     * @dataProvider providerGetRouteException
     */
    public function testGetRouteException(string $name, array $params): void
    {
        $uri     = Uri::create('http://test.com/');
        $request = new Request('GET', $uri);

        $this->object->setRequest($request);

        $this->expectException(\Exception::class);
        $this->object->getRoute($name, $params);
    }

    public function providerGetRouteException(): \Generator
    {
        yield [ 'error', [] ];
        yield [ 'test.page', [ ':id' => 'error' ] ];
        yield [ 'test.page', [ ':error' => 1 ] ];
    }

    public function testIsRewrite(): void
    {
        $uri     = Uri::create('http://test.com/');
        $request = new Request('GET', $uri, [ 'HTTP_MOD_REWRITE' => 'On' ]);

        $this->object->setRequest($request)
            ->setBasePath('http://test.com/')
            ->setConfig([ 'settings.rewrite_engine' => 'on' ]);

        $this->assertTrue($this->object->isRewrite());
        $this->assertEquals(
            'http://test.com/page/1',
            $this->object->getRoute('test.page', [ ':id' => '1' ])
        );
    }

    public function testGetBasePath(): void
    {
        $uri     = Uri::create('http://test.com/');
        $request = new Request('GET', $uri, [ 'HTTP_MOD_REWRITE' => 'On' ]);

        $this->object->setRequest($request);
        $this->assertEquals('', $this->object->getBasePath());
    }

    public function testSetConfigInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->object->setConfig('error');
    }

    public function testMakeRoute(): void
    {
        $url = $this->object
            ->setBasePath('http://test.com')
            ->makeRoute('foo/route');

        $this->assertEquals('http://test.com?q=foo/route', $url);
    }
}
