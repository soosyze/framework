<?php

use Soosyze\Components\Router\RouteCollection;
use Soosyze\Components\Router\RouteGroup;

RouteCollection::setNamespace('Soosyze\Tests\Resources\App\TestController')->group(function (RouteGroup $r) {
    $r->get('test', '/index', '@index');
    $r->get('test.json', '/json', '@getApi');
});
