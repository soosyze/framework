<?php

namespace Soosyze\Tests\Resources\App;

use Soosyze\Module;

class TestModule implements Module
{
    public function getModuleDir(): string
    {
        return __DIR__;
    }

    public function getPathServices(): string
    {
        return __DIR__ . '/config/services.php';
    }

    public function getPathRoutes(): string
    {
        return __DIR__ . '/config/routes.php';
    }
}
