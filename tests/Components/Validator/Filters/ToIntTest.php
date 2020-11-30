<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToIntTest extends Filter
{
    public function testToInt()
    {
        $this->object->setInputs([
            /* Standard */
            'int'      => 1234,
            'txt'      => '1234',
            'octal'    => 0123,
            'hexa'     => 0x1A,
            'binaire'  => 0b11111111,
            'zero'     => 0,
            'zero_txt' => '0',
            /* Cast type */
            'cast'     => (int) 1.1,
            'cast_txt' => (int) '1.1',
            /* Limit */
            'min'      => PHP_INT_MIN,
            'max'      => PHP_INT_MAX
        ])->setRules([
            /* Standard */
            'int'      => 'to_int',
            'txt'      => 'to_int',
            'octal'    => 'to_int',
            'hexa'     => 'to_int',
            'binaire'  => 'to_int',
            'zero'     => 'to_int',
            'zero_txt' => 'to_int',
            /* Cast type */
            'cast'     => 'to_int',
            'cast_txt' => 'to_int',
            /* Limit */
            'min'      => 'to_int',
            'max'      => 'to_int'
        ])->isValid();

        /* Standard */
        $this->assertTrue(is_int($this->object->getInput('int')));
        $this->assertTrue(is_int($this->object->getInput('txt')));
        $this->assertTrue(is_int($this->object->getInput('octal')));
        $this->assertTrue(is_int($this->object->getInput('hexa')));
        $this->assertTrue(is_int($this->object->getInput('binaire')));
        $this->assertTrue(is_int($this->object->getInput('zero')));
        $this->assertTrue(is_int($this->object->getInput('zero_txt')));
        /* Cast type */
        $this->assertTrue(is_int($this->object->getInput('cast')));
        $this->assertTrue(is_int($this->object->getInput('cast_txt')));
        /* Limit */
        $this->assertTrue(is_int($this->object->getInput('min')));
        $this->assertTrue(is_int($this->object->getInput('max')));
    }
    
    /**
     * @expectedException \Exception
     */
    public function testToIntException()
    {
        $this->object
            ->addInput('field', 'error')
            ->addRule('field', 'to_int')
            ->isValid();
    }
}
