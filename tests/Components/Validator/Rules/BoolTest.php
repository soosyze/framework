<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class BoolTest extends Rule
{
    public function testBool(): void
    {
        $this->object->setInputs([
            /* True */
            'true'       => true,
            'true_text'  => 'true',
            'one'        => 1,
            'one_text'   => '1',
            'on'         => 'on',
            'yes'        => 'yes',
            /* False */
            'false'      => false,
            'false_text' => 'false',
            'zero'       => 0,
            'zero_text'  => '0',
            'off'        => 'off',
            'no'         => 'no',
            'void'       => ''
        ])->setRules([
            /* True */
            'true'       => 'bool',
            'text'       => 'bool',
            'one'        => 'bool',
            'one_text'   => 'bool',
            'on'         => 'bool',
            'yes'        => 'bool',
            /* False */
            'false'      => 'bool',
            'false_text' => 'bool',
            'zero'       => 'bool',
            'zero_text'  => 'bool',
            'off'        => 'bool',
            'no'         => 'bool',
            'void'       => 'bool'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not bool',
            'not_must' => true
        ])->setRules([
            'must'     => 'bool',
            'not_must' => '!bool'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
