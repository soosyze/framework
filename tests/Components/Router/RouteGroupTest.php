<?php

namespace Soosyze\Tests\Components\Router;

use Soosyze\Components\Router\Route;
use Soosyze\Components\Router\RouteCollection;
use Soosyze\Components\Router\RouteGroup;
use Soosyze\Tests\Resources\Router\TestController;

class RouteGroupTest extends \PHPUnit\Framework\TestCase
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
        $this->collection->group(static function (RouteGroup $r): void {
            $r->setNamespace(TestController::class)->group(static function (RouteGroup $r): void {
                $r->get('index', '/', '@index');
            });
        });

        $this->assertEquals(
            json_encode([
                'index' => new Route('index', 'get', '/', TestController::class . '@index'),
            ]),
            json_encode($this->collection->getRoutes())
        );
    }

    public function testName(): void
    {
        $this->collection->name('test.')->group(static function (RouteGroup $r): void {
            $r->name('page.')->group(static function (RouteGroup $r): void {
                $r->get('index', '/', TestController::class . '@index');
            });
        });

        $this->assertEquals(
            json_encode([
                'test.page.index' => new Route(
                    'test.page.index',
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
        $this->collection->prefix('/test')->group(static function (RouteGroup $r): void {
            $r->prefix('/page')->group(static function (RouteGroup $r): void {
                $r->get('index', '/', TestController::class . '@index');
            });
        });

        $this->assertEquals(
            json_encode([
                'index' => new Route(
                    'index',
                    'get',
                    '/test/page',
                    TestController::class . '@index'
                ),
            ]),
            json_encode($this->collection->getRoutes())
        );
    }

    public function testWiths(): void
    {
        $this->collection->withs(self::WITHS)->group(static function (RouteGroup $r): void {
            $r->withs(['ext' => 'json|csv'])->group(static function (RouteGroup $r): void {
                $r->get(
                    'index',
                    '/{ext}.{id}',
                    TestController::class . '@index'
                );
            });
        });

        $this->assertEquals(
            json_encode([
                'index' => new Route(
                    'index',
                    'get',
                    '/{ext}.{id}',
                    TestController::class . '@index',
                    self::WITHS + ['ext' => 'json|csv']
                ),
            ]),
            json_encode($this->collection->getRoutes())
        );
    }
}
