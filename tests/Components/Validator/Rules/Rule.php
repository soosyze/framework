<?php

namespace Soosyze\Tests\Components\Validator\Rules;

use Soosyze\Components\Validator\Validator;

if (!defined('PHP_INT_MIN')) {
    define('PHP_INT_MIN', -PHP_INT_MAX - 1);
}

class Rule extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Validator
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new Validator;
    }
}
