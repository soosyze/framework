<?php

namespace Soosyze\Tests\Components\Validator\Rules;

use Soosyze\Components\Validator\Validator;

class Rule extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Validator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Validator;
    }
}
