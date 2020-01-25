<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class ArrayTest extends Rule
{
    public function testArray()
    {
        $this->object->setInputs([
            'must'              => [ 0, 1, 2 ],
            'not_must'          => 'not array',
            'required_must'     => [ 0, 1, 2 ],
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'array',
            'not_must'          => '!array',
            'required_must'     => 'required|array',
            'not_required_must' => '!required|array'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not array',
            'not_must' => [ 0, 1, 2 ]
        ])->setRules([
            'must'     => 'array',
            'not_must' => '!array'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
