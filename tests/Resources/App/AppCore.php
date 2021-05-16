<?php

namespace Soosyze\Tests\Resources\App;

class AppCore extends \Soosyze\App
{
    protected function loadModules(): array
    {
        return [
            new TestController()
        ];
    }

    protected function loadServices(): array
    {
        return [];
    }
}
