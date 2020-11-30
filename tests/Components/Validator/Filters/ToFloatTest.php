<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToFloatTest extends Filter
{
    public function testToFloat()
    {
        $this->object->setInputs([
            /* Standard */
            'float'       => 1.0,
            'txt'         => '1.0',
            'decimal'     => 1,
            'decimal_txt' => '1',
            'zero'        => 0,
            'zero_txt'    => '0',
            /* Exponent */
            'exp'         => 1.0e1,
            'exp_2'       => 1E1,
            'exp_txt'     => '1.0e1',
            'exp_txt_2'   => '1E1',
            /* Limit */
            'min'         => PHP_INT_MIN - 1,
            'max'         => PHP_INT_MAX + 1,
            /* Cast type */
            'cast'        => (float) 1,
            'cast_txt'    => (float) '1'
        ])->setRules([
            /* Standard */
            'float'       => 'to_float',
            'txt'         => 'to_float',
            'decimal'     => 'to_float',
            'decimal_txt' => 'to_float',
            'zero'        => 'to_float',
            'zero_txt'    => 'to_float',
            /* Exponent */
            'exp'         => 'to_float',
            'exp_2'       => 'to_float',
            'exp_txt'     => 'to_float',
            'exp_txt_2'   => 'to_float',
            /* Limit */
            'min'         => 'to_float',
            'max'         => 'to_float',
            /* Cast type */
            'cast'        => 'to_float',
            'cast_txt'    => 'to_float'
        ])->isValid();

        /* Standard */
        $this->assertTrue(is_float($this->object->getInput('float')));
        $this->assertTrue(is_float($this->object->getInput('txt')));
        $this->assertTrue(is_float($this->object->getInput('decimal')));
        $this->assertTrue(is_float($this->object->getInput('decimal_txt')));
        $this->assertTrue(is_float($this->object->getInput('zero')));
        $this->assertTrue(is_float($this->object->getInput('zero_txt')));
        /* Exponent */
        $this->assertTrue(is_float($this->object->getInput('exp')));
        $this->assertTrue(is_float($this->object->getInput('exp_2')));
        $this->assertTrue(is_float($this->object->getInput('exp_txt')));
        $this->assertTrue(is_float($this->object->getInput('exp_txt_2')));
        /* Limit */
        $this->assertTrue(is_float($this->object->getInput('min')));
        $this->assertTrue(is_float($this->object->getInput('max')));
        /* Cast type */
        $this->assertTrue(is_float($this->object->getInput('cast')));
        $this->assertTrue(is_float($this->object->getInput('cast_txt')));
    }

    /**
     * @expectedException \Exception
     */
    public function testToFloatException()
    {
        $this->object
            ->addInput('field', 'error')
            ->addRule('field', 'to_float')
            ->isValid();
    }
}
