<?php

namespace Soosyze\Tests\Resources\Container;

class Service1
{
    /**
     * @var int|null
     */
    private $number = null;

    public function isOk(): bool
    {
        return true;
    }

    public function hookDouble(int &$var): void
    {
        $var *= 2;
    }

    public function setData(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getData(): ?int
    {
        return $this->number;
    }
}
