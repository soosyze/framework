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

        $this->assertEquals(['Soosyze\Tests\Resources\Router\TestController', 'index'], $route->getCallable());
        $this->assertEquals('index', $route->getKey());
        $this->assertEquals('get', $route->getMethod());
        $this->assertEquals('/', $route->getPath());
        $this->assertEquals('Soosyze\Tests\Resources\Router\TestController@index', $route->getUses());
        $this->assertEquals(
            ['digit' => '\d+', 'words' => '\w+', 'slug' => '[a-z\d\-]+'],
            $route->getWiths()
        );
    }
}
