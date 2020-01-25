<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class NumericTest extends Rule
{
    public function testNumeric()
    {
        $this->object->setInputs([
            /* -------- STANDARD ------- */
            'decimal'         => 1,
            'decimal_txt'     => '1',
            'zero'            => 0,
            'zero_txt'        => '0',
            'float_not'       => '',
            /* --------- FLOAT --------- */
            /* Standard */
            'float'           => 1.0,
            'float_txt'       => '1.0',
            /* Exponent */
            'float_exp'       => 1.0e1,
            'float_exp_2'     => 1E1,
            'float_exp_txt'   => '1.0e1',
            'float_exp_txt_2' => '1E1',
            /* Limit */
            'float_min'       => -PHP_INT_MAX - 2, // PHP_INT_MIN - 1
            'float_max'       => PHP_INT_MAX + 1,
            /* Cast type */
            'float_cast'      => (float) 1,
            'float_cast_txt'  => (float) '1',
            /* ---------- INT ---------- */
            /* Standard */
            'int_octal'       => 0123,
            'int_hexa'        => 0x1A,
            'int_binaire'     => 0b11111111,
            /* Cast type */
            'int_cast'        => (int) 1.1,
            'int_cast_txt'    => (int) '1.1',
            /* Limit */
            'int_min'         => -PHP_INT_MAX - 1, // PHP_INT_MIN
            'int_max'         => PHP_INT_MAX
        ])->setRules([
            /* -------- STANDARD ------- */
            'decimal'         => 'numeric',
            'decimal_txt'     => 'numeric',
            'zero'            => 'numeric',
            'zero_txt'        => 'numeric',
            'float_not'       => '!numeric',
            /* --------- FLOAT --------- */
            /* Standard */
            'float'           => 'numeric',
            'float_txt'       => 'numeric',
            /* Exponent */
            'float_exp'       => 'numeric',
            'float_exp_2'     => 'numeric',
            'float_exp_txt'   => 'numeric',
            'float_exp_txt_2' => 'numeric',
            /* Limit */
            'float_min'       => 'numeric',
            'float_max'       => 'numeric',
            /* Cast type */
            'float_cast'      => 'numeric',
            'float_cast_txt'  => 'numeric',
            /* ---------- INT ---------- */
            /* Standard */
            'int_octal'       => 'numeric',
            'int_hexa'        => 'numeric',
            'int_binaire'     => 'numeric',
            /* Cast type */
            'int_cast'        => 'numeric',
            'int_cast_txt'    => 'numeric',
            /* Limit */
            'int_min'         => 'numeric',
            'int_max'         => 'numeric',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not bool',
            'not_must' => 1
        ])->setRules([
            'must'     => 'numeric',
            'not_must' => '!numeric'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
