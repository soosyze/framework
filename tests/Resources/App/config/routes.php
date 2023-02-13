<?php

use Soosyze\Components\Router\RouteCollection;
use Soosyze\Components\Router\RouteGroup;
use Soosyze\Components\Router\RouterInterface;
use Soosyze\Tests\Resources\App\TestController;

return new class implements RouterInterface {
    public function getRoutes(RouteCollection $rc): void
    {
        $rc->setNamespace(TestController::class)->group(function (RouteGroup $r) {
            $r->get('test', '/index', '@index');
            $r->get('test.json', '/json', '@getApi');
        });
    }
};
