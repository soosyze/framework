<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class FloatTest extends Rule
{
    public function testFloat()
    {
        $this->object->setInputs([
            /* Standard */
            'float'        => 1.0,
            'txt'          => '1.0',
            'decimal'      => 1,
            'decimal_txt'  => '1',
            'zero'         => 0,
            'zero_txt'     => '0',
            /* Exponent */
            'exp'          => 1.0e1,
            'exp_2'        => 1E1,
            'exp_txt'      => '1.0e1',
            'exp_txt_2'    => '1E1',
            /* Limit */
            'min'          => PHP_INT_MIN - 1,
            'max'          => PHP_INT_MAX + 1,
            /* Cast type */
            'cast'         => (float) 1,
            'cast_txt'     => (float) '1',
            /* Other */
            'not'          => '',
            'required'     => 1.1,
            'not_required' => ''
        ])->setRules([
            /* Standard */
            'float'        => 'float',
            'txt'          => 'float',
            'decimal'      => 'float',
            'decimal_txt'  => 'float',
            'zero'         => 'float',
            'zero_txt'     => 'float',
            /* Exponent */
            'exp'          => 'float',
            'exp_2'        => 'float',
            'exp_txt'      => 'float',
            'exp_txt_2'    => 'float',
            /* Limit */
            'min'          => 'float',
            'max'          => 'float',
            /* Cast type */
            'cast'         => 'float',
            'cast_txt'     => 'float',
            /* Other */
            'not'          => '!float',
            'required'     => 'required|float',
            'not_required' => '!required|float'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not float',
            'not_must' => 10.1
        ])->setRules([
            'must'     => 'float',
            'not_must' => '!float'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
