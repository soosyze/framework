<?php

namespace Soosyze\Tests\Resources\App;

class AppCore extends \Soosyze\App
{
    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function loadModules(): iterable
    {
        yield new TestModule();
    }

    protected function loadServices(): array
    {
        return [];
    }
}
