<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class InArrayTest extends Rule
{
    public function testInArray(): void
    {
        $this->object->setInputs([
            'must'              => '1',
            'not_must'          => '1',
            'required_must'     => '1',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'inarray:1,2,3,4,5',
            'not_must'          => '!inarray:2,3,4,5',
            'required_must'     => 'required|inarray:1,2,3,4,5',
            'not_required_must' => '!required|inarray:1,2,3,4,5'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => '1',
            'not_must' => '1'
        ])->setRules([
            'must'     => 'inarray:2,3,4,5',
            'not_must' => '!inarray:1,2,3,4,5'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
