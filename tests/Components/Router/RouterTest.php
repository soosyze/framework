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
        Route::useNamespace('')->name('test.')->group(function (): void {
            Route::get('index', '/', '\Soosyze\Tests\Resources\Router\TestController@index');
            Route::post('post', '/', '\Soosyze\Tests\Resources\Router\TestController@indexPost');
        });

        Route::useNamespace('Soosyze\Tests\Resources\Router')->name('test.')->prefix('/page')->group(function (): void {
            Route::get('page', '/:id', 'TestController@page', [ ':id' => '[0-9]+' ]);
            Route::post('post', '/:id/post', 'TestController@post', [ ':id' => '[0-9]+' ]);
            Route::put('put', '/:id/put', 'TestController@put', [ ':id' => '[0-9]+' ]);
            Route::patch('patch', '/:id/patch', 'TestController@patch', [ ':id' => '[0-9]+' ]);
            Route::option('option', '/:id/option', 'TestController@option', [ ':id' => '[0-9]+' ]);
            Route::delete('delete', '/:id/delete', 'TestController@delete', [ ':id' => '[0-9]+' ]);
            Route::get('page.format', '/:id.:ext', 'TestController@format', [
                ':id'  => '[0-9]+',
                ':ext' => 'json|xml'
            ]);
        });
    }

    public function testParse(): void
    {
        $request = new Request('GET', Uri::create('http://test.com'));

        $expectedRoute = [
            'key'    => 'test.index',
            'method' => 'get',
            'path'   => '/',
            'uses'   => "\Soosyze\Tests\Resources\Router\TestController@index",
            'with'   => []
        ];

        $this->assertEquals($expectedRoute, $this->object->parse($request));

        $request = new Request('GET', Uri::create('http://test.com/404'));

        $this->assertNull($this->object->parse($request));
    }

    public function testParseQueryFromRequest(): void
    {
        $request = new Request('GET', Uri::create('http://test.com'));

        $this->assertEquals(
            '/',
            $this->object->setRequest($request)->parseQueryFromRequest()
        );

        $request = new Request('GET', Uri::create('http://test.com/foo'));

        $this->assertEquals(
            '/foo',
            $this->object->setRequest($request)->parseQueryFromRequest()
        );
    }

    /**
     * @dataProvider providerExecute
     */
    public function testExecute(string $expected, string $uri): void
    {
        $request = new Request('GET', Uri::create($uri));

        $route  = $this->object->parse($request);
        $result = $this->object->execute($route ?? [], $request);

        $this->assertEquals($expected, $result);
    }

    public function providerExecute(): \Generator
    {
        yield [ 'hello world !', 'http://test.com' ];
        yield [ 'hello page 1', 'http://test.com/page/1' ];
        yield [ 'hello page 1', 'http://test.com/page/1?s=title#foo' ];
        yield [ 'hello json 1', 'http://test.com/page/1.json' ];
    }

    public function testExecuteSetRequest(): void
    {
        $request = new Request('GET', Uri::create('http://test.com'));

        $route  = $this->object->parse($request);
        $result = $this->object->setRequest($request)->execute($route ?? []);

        $this->assertEquals('hello world !', $result);
    }

    public function testExecuteExceptionNotRequest(): void
    {
        $request = new Request('GET', Uri::create('http://test.com/page/1'));

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
        $request = new Request('GET', Uri::create('http://test.com/test'));

        $this->object->setRequest($request)->setBasePath('http://test.com');

        $this->assertEquals(
            'http://test.com/',
            $this->object->getRoute('test.index')
        );
    }

    public function testGetRequestByRoute(): void
    {
        $request = new Request('GET', Uri::create('http://test.com/test'));

        $this->object->setRequest($request)->setBasePath('http://test.com');
        $result = $this->object->getRequestByRoute('test.index');

        $this->assertEquals('http://test.com/', (string) $result->getUri());
    }

    public function testGetRouteStrictParam(): void
    {
        $request = new Request('GET', Uri::create('http://test.com/test'));

        $this->object->setRequest($request)->setBasePath('http://test.com');

        $this->assertEquals(
            'http://test.com/page/1',
            $this->object->getRoute('test.page', [ ':id' => '1' ])
        );
    }

    public function testGetRouteParam(): void
    {
        $request = new Request('GET', Uri::create('http://test.com/test'));

        $this->object->setRequest($request)->setBasePath('http://test.com');

        $this->assertEquals(
            'http://test.com/page/:id.json',
            $this->object->getRoute('test.page.format', [ ':ext' => 'json' ], false)
        );
    }

    /**
     * @dataProvider providerGetRouteException
     */
    public function testGetRouteException(string $name, array $params): void
    {
        $request = new Request('GET', Uri::create('http://test.com'));

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

    public function testGetBasePath(): void
    {
        $request = new Request('GET', Uri::create('http://test.com/'));

        $this->object->setRequest($request);
        $this->assertEquals('', $this->object->getBasePath());
    }

    public function testMakeRoute(): void
    {
        $url = $this->object
            ->setBasePath('http://test.com')
            ->makeRoute('/foo/route');

        $this->assertEquals('http://test.com/foo/route', $url);
    }
}
