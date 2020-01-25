<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class IntTest extends Rule
{
    public function testInt()
    {
        $this->object->setInputs([
            /* Standard */
            'int'          => 1234,
            'txt'          => '1234',
            'octal'        => 0123,
            'hexa'         => 0x1A,
            'binaire'      => 0b11111111,
            'zero'         => 0,
            'zero_txt'     => '0',
            /* Limit */
            'min'          => -PHP_INT_MAX - 1, // PHP_INT_MIN
            'max'          => PHP_INT_MAX,
            /* Cast type */
            'cast'         => (int) 1.1,
            'cast_txt'     => (int) '1.1',
            /* Other */
            'not_must'     => 1.1,
            'required'     => 1,
            'not_required' => ''
        ])->setRules([
            /* Standard */
            'int'          => 'int',
            'txt'          => 'int',
            'octal'        => 'int',
            'hexa'         => 'int',
            'binaire'      => 'int',
            'zero'         => 'int',
            'zero_txt'     => 'int',
            /* Limit */
            'min'          => 'int',
            'max'          => 'int',
            /* Cast type */
            'cast'         => 'int',
            'cast_txt'     => 'int',
            /* Other */
            'not_must'     => '!int',
            'required'     => 'required|int',
            'not_required' => '!required|int'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'int'         => 1,
            'int_octal'   => '0123',
            'int_hexa'    => '0x1A',
            'int_binaire' => '0b11111111',
            'float'       => 1.1,
            'float_min'   => PHP_INT_MIN - 1,
            'float_max'   => PHP_INT_MAX + 1
        ])->setRules([
            'int'         => '!int',
            'float'       => 'int',
            'int_octal'   => 'int',
            'int_hexa'    => 'int',
            'int_binaire' => 'int',
            'float_min'   => 'int',
            'float_max'   => 'int'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(7, $this->object->getErrors());
    }
}
