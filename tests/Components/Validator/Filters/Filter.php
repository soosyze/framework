<?php

namespace Soosyze\Tests\Components\Validator\Filters;

use Soosyze\Components\Validator\Validator;

class Filter extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Validator
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Validator;
    }
}
