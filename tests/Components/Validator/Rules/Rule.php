<?php

namespace Soosyze\Tests\Components\Validator\Rules;

use Soosyze\Components\Validator\Validator;

class Rule extends \PHPUnit\Framework\TestCase
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
