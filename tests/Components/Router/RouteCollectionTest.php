<?php

namespace Soosyze\Tests\Components\Router;

use Soosyze\Components\Router\Route;
use Soosyze\Components\Router\RouteCollection;
use Soosyze\Components\Router\RouteGroup;
use Soosyze\Tests\Resources\Router\TestController;

class RouteCollectionTest extends \PHPUnit\Framework\TestCase
{
    private const WITHS = ['id' => '\d+'];

    /** @var RouteCollection */
    protected $collection;

    protected function setUp(): void
    {
        $this->collection = new RouteCollection();
    }

    public function testNamespace(): void
    {
        $this->collection->setNamespace(TestController::class)->group(static function (RouteGroup $r): void {
            $r->get('page', '/', '@page');
            $r->post('post', '/post', '@post');
            $r->put('put', '/put', '@put');
            $r->patch('patch', '/patch', '@patch');
            $r->option('option', '/option', '@option');
            $r->delete('delete', '/delete', '@delete');
        });

        $this->assertEquals(
            json_encode([
                'page' => new Route('page', 'get', '/', TestController::class . '@page'),
                'post' => new Route('post', 'post', '/post', TestController::class . '@post'),
                'put' => new Route('put', 'put', '/put', TestController::class . '@put'),
                'patch' => new Route('patch', 'patch', '/patch', TestController::class . '@patch'),
                'option' => new Route('option', 'option', '/option', TestController::class . '@option'),
                'delete' => new Route('delete', 'delete', '/delete', TestController::class . '@delete'),
            ]),
            json_encode($this->collection->getRoutes())
        );
    }

    public function testRouteExecpton(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method error does not exist');

        $this->collection->group(static function (RouteGroup $r): void {
            /* @phpstan-ignore-next-line */
            $r->error('page', '/', '@page');
        });
    }

    public function testName(): void
    {
        $this->collection->name('test.')->group(static function (RouteGroup $r): void {
            $r->get('index', '/', TestController::class . '@index');
        });

        $this->assertEquals(
            json_encode([
                'test.index' => new Route(
                    'test.index',
                    'get',
                    '/',
                    TestController::class . '@index'
                ),
            ]),
            json_encode($this->collection->getRoutes())
        );
    }

    public function testPrefix(): void
    {
        $this->collection->prefix('/page')->group(static function (RouteGroup $r): void {
            $r->get('index', '/', TestController::class . '@index');
        });

        $this->assertEquals(
            json_encode([
                'index' => new Route(
                    'index',
                    'get',
                    '/page',
                    TestController::class . '@index'
                ),
            ]),
            json_encode($this->collection->getRoutes())
        );
    }

    public function testWiths(): void
    {
        $this->collection->withs(self::WITHS)->group(static function (RouteGroup $r): void {
            $r->get('index', '/{id}', TestController::class . '@index');
        });

        $this->assertEquals(
            json_encode([
                'index' => new Route(
                    'index',
                    'get',
                    '/{id}',
                    TestController::class . '@index',
                    self::WITHS
                ),
            ]),
            json_encode($this->collection->getRoutes())
        );
    }
}
