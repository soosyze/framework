<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class NullTest extends Rule
{
    public function testNull()
    {
        $this->object->setInputs([
            'must'              => null,
            'not_must'          => '',
            'required_must'     => null,
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'null',
            'not_must'          => '!null',
            'required_must'     => 'required|null',
            'not_required_must' => '!required|null'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'          => '',
            'not_must'      => null,
            'required_must' => ''
        ])->setRules([
            'must'          => 'null',
            'not_must'      => '!null',
            'required_must' => 'required|null'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getKeyErrors());
    }
}
