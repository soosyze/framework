<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class EqualsTest extends Rule
{
    public function testEqual()
    {
        $this->object->setInputs([
            /* Standard */
            'must_str'          => 'hello',
            'must_num'          => '1',
            'must_int'          => 1,
            'must_float'        => '1.1',
            'must_bool'         => true,
            'must_bool_f'       => false,
            'must_ref'          => 'hello',
            /* Integration */
            'not_must'          => 'not hello',
            'required_must'     => 'hello',
            'not_required_must' => ''
        ])->setRules([
            /* Standard */
            'must_str'          => 'equal:hello',
            'must_num'          => 'equal:1',
            'must_int'          => 'equal:1',
            'must_float'        => 'equal:1.1',
            'must_bool'         => 'equal:true',
            'must_bool_f'       => 'equal:0', // 0 == false
            'must_ref'          => 'equal:@must_str',
            /* Integration */
            'not_must'          => '!equal:hello',
            'required_must'     => 'required|equal:hello',
            'not_required_must' => '!required|equal:hello'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not hello',
            'not_must' => 'hello'
        ])->setRules([
            'must'     => 'equal:hello',
            'not_must' => '!equal:hello'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testEqualStrict()
    {
        $this->object->setInputs([
            /* Standard */
            'must_str'       => 'hello',
            'must_num_int'   => '1',
            'must_num_float' => '1.1',
            /* Reference */
            'must_ref_int'   => 1,
            'must_ref_float' => 1.1,
            'must_ref_bool'  => true,
            /* Field for reference. */
            'field_int'      => 1,
            'field_float'    => 1.1,
            'field_bool'     => true
        ])->setRules([
            /* Standard */
            'must_str'       => 'equal_strict:hello',
            'must_num_int'   => 'equal_strict:1',
            'must_num_float' => 'equal_strict:1.1',
            /* Reference */
            'must_ref_int'   => 'equal_strict:@field_int',
            'must_ref_float' => 'equal_strict:@field_float',
            'must_ref_bool'  => 'equal_strict:@field_bool'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must_str' => 'not hello',
            'must_int' => 1,
            'not_must' => 'hello'
        ])->setRules([
            'must_str' => 'equal_strict:hello',
            'must_int' => 'equal_strict:1',
            'not_must' => '!equal_strict:hello'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }
}
