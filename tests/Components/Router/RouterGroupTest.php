<?php

namespace Soosyze\Tests\Components\Router;

use Soosyze\Components\Router\Route;
use Soosyze\Components\Router\RouteCollection;
use Soosyze\Components\Router\RouteGroup;

class RouterGroupTest extends \PHPUnit\Framework\TestCase
{
    private const WITHS = [ ':id' => '\d+' ];

    public function testGetRoutes(): void
    {
        RouteCollection::group(function (RouteGroup $r): void {
            $r->get('index', '/', 'Soosyze\Tests\Resources\Router\TestController@index');
            $r->post('filter', '/', 'Soosyze\Tests\Resources\Router\TestController@filter');
        });
        RouteCollection::setNamespace('Soosyze\Tests\Resources\Router')->name('test.')->prefix('/page')->group(function (RouteGroup $r): void {
            $r->prefix('/foo');
            $r->get('index', '/', '\TestController@index');

            $r->prefix('/:id')->withs(self::WITHS)->group(function (RouteGroup $r) {
                $r->setNamespace('\TestController')->group(function (RouteGroup $r) {
                    $r->get('page', '/', '@page');
                    $r->post('post', '/post', '@post');
                    $r->put('put', '/put', '@put');
                    $r->patch('patch', '/patch', '@patch');
                    $r->option('option', '/option', '@option');
                    $r->delete('delete', '/delete', '@delete');
                });

                $r->prefix('/api')->setNamespace('\ApiController')->name('api.')->group(function (RouteGroup $r) {
                    $r->get('json', '/:format', '@format', [ ':format' => 'json' ]);
                    $r->get('xml', '/:format', '@format', [ ':format' => 'xml' ]);
                });
            });
        });

        $expected = [
            'test'          => new Route('test', 'get', '/index', 'Soosyze\Tests\Resources\App\TestController@index'),
            'test.json'     => new Route('test.json', 'get', '/json', 'Soosyze\Tests\Resources\App\TestController@getApi'),
            'index'         => new Route('index', 'get', '/', 'Soosyze\Tests\Resources\Router\TestController@index'),
            'filter'        => new Route('filter', 'post', '/', 'Soosyze\Tests\Resources\Router\TestController@filter'),
            'test.index'    => new Route('test.index', 'get', '/page', 'Soosyze\Tests\Resources\Router\TestController@index'),
            'test.page'     => new Route('test.page', 'get', '/page/:id', 'Soosyze\Tests\Resources\Router\TestController@page', self::WITHS),
            'test.post'     => new Route('test.post', 'post', '/page/:id/post', 'Soosyze\Tests\Resources\Router\TestController@post', self::WITHS),
            'test.put'      => new Route('test.put', 'put', '/page/:id/put', 'Soosyze\Tests\Resources\Router\TestController@put', self::WITHS),
            'test.patch'    => new Route('test.patch', 'patch', '/page/:id/patch', 'Soosyze\Tests\Resources\Router\TestController@patch', self::WITHS),
            'test.option'   => new Route('test.option', 'option', '/page/:id/option', 'Soosyze\Tests\Resources\Router\TestController@option', self::WITHS),
            'test.delete'   => new Route('test.delete', 'delete', '/page/:id/delete', 'Soosyze\Tests\Resources\Router\TestController@delete', self::WITHS),
            'test.api.json' => new Route(
                'test.api.json',
                'get',
                '/page/:id/api/:format',
                'Soosyze\Tests\Resources\Router\ApiController@format',
                self::WITHS + [ ':format' => 'json' ]
            ),
            'test.api.xml'  => new Route(
                'test.api.xml',
                'get',
                '/page/:id/api/:format',
                'Soosyze\Tests\Resources\Router\ApiController@format',
                self::WITHS + [ ':format' => 'xml' ]
            )
        ];
        $this->assertEquals(
            json_encode($expected),
            json_encode(RouteCollection::getRoutes())
        );
    }
}
