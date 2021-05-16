<?php

namespace Soosyze\Tests\Resources\Container;

class Service3
{
    /**
     * @var Service1
     */
    protected $service1;

    /**
     * @var string
     */
    protected $str;

    public function __construct(Service1 $service1, string $str)
    {
        $this->service1 = $service1;
        $this->str      = $str;
    }

    public function getStr(): string
    {
        return $this->str;
    }
}
