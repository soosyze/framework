<?php

namespace Soosyze\Tests\Resources\Container;

class Service2
{
    /**
     * @var Service1
     */
    protected $service1;

    public function __construct(Service1 $service1)
    {
        $this->service1 = $service1;
    }

    public function isOk(): bool
    {
        return $this->service1->isOk();
    }
}
