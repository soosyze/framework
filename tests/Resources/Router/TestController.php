<?php

namespace Soosyze\Tests\Resources\Router;

class TestController
{
    public function index(): string
    {
        return 'hello world !';
    }

    public function format(string $item, string $ext): string
    {
        return "hello $ext $item";
    }

    public function page(string $item): string
    {
        return 'hello page ' . $item;
    }
}
