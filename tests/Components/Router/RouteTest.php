<?php

namespace Soosyze\Tests\Components\Router;

use Soosyze\Components\Router\Route;

class RouteTest extends \PHPUnit\Framework\TestCase
{
    public function testRouteGetter(): void
    {
        $route = (new Route('index', 'get', '/', 'Soosyze\Tests\Resources\Router\TestController@index'))
            ->whereDigits('digit')
            ->whereWords('words')
            ->whereSlug('slug');

        $this->assertEquals([ 'Soosyze\Tests\Resources\Router\TestController', 'index' ], $route->getCallable());
        $this->assertEquals('index', $route->getKey());
        $this->assertEquals('get', $route->getMethod());
        $this->assertEquals('/', $route->getPath());
        $this->assertEquals('Soosyze\Tests\Resources\Router\TestController@index', $route->getUses());
        $this->assertEquals(
            [ 'digit' => '\d+', 'words' => '\w+', 'slug' => '[a-z\d\-]+' ],
            $route->getWiths()
        );
    }

    /**
     * @dataProvider getRegexForPathProvider
     */
    public function testGetRegexForPath(string $pathExpected, Route $route): void
    {
        $this->assertEquals($pathExpected, $route->getRegexForPath());
    }

    public function getRegexForPathProvider(): \Generator
    {
        yield 'without parentheses' => [
            '\/index\/page\/(?<id>\d+)\/edit',
            new Route(
                'edit',
                'get',
                '/index/page/{id}/edit',
                '\TestController@edit',
                [ 'id' => '\d+' ]
            )
        ];
        yield 'with parentheses' => [
            '\/index\/page\/(?<id>(?:\d+))\/edit',
            new Route(
                'edit',
                'get',
                '/index/page/{id}/edit',
                '\TestController@edit',
                [ 'id' => '(\d+)' ]
            )
        ];
        yield 'without parameters' => [
            '\/index\/page',
            new Route(
                'edit',
                'get',
                '/index/page',
                '\TestController@page'
            )
        ];
    }

    /**
     * @dataProvider getGeneratePathProvider
     */
    public function testGeneratePath(
        string $pathExpected,
        Route $route,
        array $withs,
        bool $strict = true
    ): void {
        $this->assertEquals($pathExpected, $route->generatePath($withs, $strict));
    }

    public function getGeneratePathProvider(): \Generator
    {
        yield 'with strict' => [
            '/index/menu/1/link/1',
            new Route(
                'index',
                'get',
                '/index/menu/{menuId}/link/{linkId}',
                '\TestController@index',
                [ 'menuId' => '\d+', 'linkId' => '\d+' ]
            ),
            [ 'menuId' => 1, 'linkId' => 1 ]
        ];
        yield 'without strict' => [
            '/index/menu/1/link/{linkId}',
            new Route(
                'index',
                'get',
                '/index/menu/{menuId}/link/{linkId}',
                '\TestController@index',
                [ 'menuId' => '\d+', 'linkId' => '\d+' ]
            ),
            [ 'menuId' => 1 ],
            false
        ];
    }

    /**
     * @dataProvider getGeneratePathExceptionProvider
     */
    public function testGeneratePathException(array $params): void
    {
        $route = new Route(
            'edit',
            'get',
            '/index/page/{id}/edit',
            '\TestController@edit',
            [ 'id' => '\d+' ]
        );

        $this->expectException(\Exception::class);
        $route->generatePath($params);
    }

    public function getGeneratePathExceptionProvider(): \Generator
    {
        yield 'without parameters' => [ [] ];
        yield 'with parameters type error' => [ [ 'id' => 'error' ] ];
        yield 'with parameters name error' => [ [ 'error' => 1 ] ];
    }
}
