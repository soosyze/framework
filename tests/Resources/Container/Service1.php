<?php

namespace Soosyze\Tests\Resources\Container;

class Service1
{
    public function isOk(): bool
    {
        return true;
    }

    public function hookDouble(int &$var): void
    {
        $var *= 2;
    }
}
