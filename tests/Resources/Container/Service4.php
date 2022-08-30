<?php

namespace Soosyze\Tests\Resources\Container;

class Service4
{
    /**
     * @var Service1
     */
    protected $service1;

    /**
     * @var string
     */
    protected $str;

    public function __construct(Service1 $service1, string $str = 'default')
    {
        $this->service1 = $service1;
        $this->str      = $str;
    }

    public function getStr(): string
    {
        return $this->str;
    }
}
