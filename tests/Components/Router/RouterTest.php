<?php

namespace Soosyze\Tests\Components\Router;

use Psr\Http\Message\RequestInterface;
use Soosyze\Components\Http\Request;
use Soosyze\Components\Http\ServerRequest;
use Soosyze\Components\Http\Uri;
use Soosyze\Components\Router\Route;
use Soosyze\Components\Router\RouteCollection;
use Soosyze\Components\Router\RouteGroup;
use Soosyze\Components\Router\Router;

class RouterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Router
     */
    protected $object;

    public static function setUpBeforeClass(): void
    {
        RouteCollection::group(function (RouteGroup $r): void {
            $r->get('index', '/', 'Soosyze\Tests\Resources\Router\TestController@index');
            $r->post('filter', '/', 'Soosyze\Tests\Resources\Router\TestController@filter');
        });
        RouteCollection::setNamespace('Soosyze\Tests\Resources\Router\TestController')->name('test.')->prefix('/page')->group(function (RouteGroup $r): void {
            $r->prefix('/{id}', [ '{id}' => '\d+' ])->group(function (RouteGroup $r) {
                $r->get('page', '/', '@page');
                $r->post('post', '/post', '@post');
                $r->put('put', '/put', '@put');
                $r->patch('patch', '/patch', '@patch');
                $r->option('option', '/option', '@option');
                $r->delete('delete', '/delete', '@delete');
                $r->get('page.format', '.{ext}', '@format', [ '{ext}' => 'json|xml' ]);
                $r->get('page.format.csv', '.csv', '@optionalFormat');
                $r->get('page.service', '/service', '@service');
                $r->get('page.request', '/request/{idRequest}', '@request')->whereDigits('{idRequest}');
            });
        });
    }

    protected function setUp(): void
    {
        $serverRequest = new ServerRequest(
            'GET',
            Uri::create('http://test.com?key=value')
        );

        $this->object = new Router($serverRequest);
    }

    /**
     * @dataProvider getParseProvider
     */
    public function testParse(Route $route, Request $request): void
    {
        $parse = $this->object->parse($request);

        $this->assertEquals($route, $parse);
    }

    public function getParseProvider(): \Generator
    {
        yield [
            new Route('index', 'get', '/', "Soosyze\Tests\Resources\Router\TestController@index"),
            new Request('GET', Uri::create('http://test.com'))
        ];
        yield [
            new Route('filter', 'post', '/', "Soosyze\Tests\Resources\Router\TestController@filter"),
            new Request('POST', Uri::create('http://test.com'))
        ];
    }

    public function testParseNotfound(): void
    {
        $this->assertNull(
            $this->object->parse(new Request('GET', Uri::create('http://test.com/404')))
        );
    }

    /**
     * @dataProvider getParseQueryFromRequestProvider
     */
    public function testParsePathFromRequest(string $expected, string $url): void
    {
        $this->object
            ->setRequest(new Request('GET', Uri::create($url)))
            ->setBasePath('');

        $this->assertEquals(
            $expected,
            $this->object->getPathFromRequest()
        );
    }

    public function getParseQueryFromRequestProvider(): \Generator
    {
        yield [ '/', 'http://test.com' ];
        yield [ '/', 'http://test.com/' ];
        yield [ '/foo', 'http://test.com/foo' ];
        yield [ '/foo', 'http://test.com/foo?page=1#anchor' ];
    }

    /**
     * @dataProvider providerExecute
     */
    public function testExecute(string $expected, string $uri): void
    {
        $request = new Request('GET', Uri::create($uri));

        /** @var Route $route */
        $route  = $this->object->parse($request);
        $result = $this->object->execute($route, $request);

        $this->assertEquals($expected, $result);
    }

    public function providerExecute(): \Generator
    {
        yield [ 'hello world !', 'http://test.com' ];
        yield [ 'hello world !', 'http://test.com/' ];
        yield [ 'page 1', 'http://test.com/page/1' ];
        yield [ 'page 1', 'http://test.com/page/1?s=title#foo' ];
        yield [ 'page 1, format json', 'http://test.com/page/1.json' ];
        yield [ 'page 1, format csv', 'http://test.com/page/1.csv' ];
        yield [ 'page 1, request 1 to method GET', 'http://test.com/page/1/request/1' ];
    }

    public function testExecuteSetRequest(): void
    {
        $request = new Request('GET', Uri::create('http://test.com'));

        /** @var Route $route */
        $route  = $this->object->parse($request);
        $result = $this->object->setRequest($request)->execute($route);

        $this->assertEquals('hello world !', $result);
    }

    public function testExecuteExceptionNotRequest(): void
    {
        $request = new Request('GET', Uri::create('http://test.com/page/1'));

        /** @var Route $route */
        $route = $this->object->parse($request);

        $this->expectException(\Exception::class);
        $this->object->execute($route);
    }

    public function testGetRegexForPath(): void
    {
        $str = 'index/page/{id}/edit';

        $this->assertEquals(
            'index\/page\/(?<id>\d+)\/edit',
            $this->object->getRegexForPath($str, [ '{id}' => '\d+' ])
        );

        $this->assertEquals(
            'index\/page\/(?<id>(?:\d+))\/edit',
            $this->object->getRegexForPath($str, [ '{id}' => '(\d+)' ])
        );
    }

    /**
     * @dataProvider getRouteProvider
     */
    public function testGetRoute(string $expected, array $params): void
    {
        $route = $this->object
            ->setRequest(new Request('GET', Uri::create('http://test.com/test')))
            ->setBasePath('http://test.com/')
            ->generateUrl(...$params);

        $this->assertEquals($expected, $route);
    }

    public function getRouteProvider(): \Generator
    {
        yield 'testGetRoute' => [
            'http://test.com/',
            [ 'index' ]
        ];
        yield 'testGetRouteStrictParam' => [
            'http://test.com/page/1',
            [ 'test.page', [ '{id}' => '1' ] ]
        ];
        yield 'testGetRouteParam' => [
            'http://test.com/page/{id}.json',
            [ 'test.page.format', [ '{ext}' => 'json' ], false ]
        ];
    }

    public function testGetRequestByRoute(): void
    {
        $requestByRoute = $this->object
            ->setRequest(new Request('GET', Uri::create('http://test.com/test')))
            ->setBasePath('http://test.com/')
            ->generateRequest('index');

        $this->assertInstanceOf(RequestInterface::class, $requestByRoute);
        $this->assertEquals('http://test.com/', (string) $requestByRoute->getUri());
    }

    /**
     * @dataProvider providerGetRouteException
     */
    public function testGetRouteException(string $name, array $params): void
    {
        $request = new Request('GET', Uri::create('http://test.com'));

        $this->object->setRequest($request);

        $this->expectException(\Exception::class);
        $this->object->generateUrl($name, $params);
    }

    public function providerGetRouteException(): \Generator
    {
        yield [ 'error', [] ];
        yield [ 'test.page', [ 'id' => 'error' ] ];
        yield [ 'test.page', [ 'error' => 1 ] ];
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
            ->makeUrl('/foo/route');

        $this->assertEquals('http://test.com/foo/route', $url);
    }
}
