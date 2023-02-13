<?php

namespace Soosyze\Components\Router;

use Soosyze\Components\Router\RouteCollection;

interface RouterInterface
{
    public function getRoutes(RouteCollection $route): void;
}
