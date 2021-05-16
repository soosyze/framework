<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class ArrayTest extends Rule
{
    public function testArray(): void
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
            'must'          => 'not array',
            'not_must'      => [ 0, 1, 2 ],
            'required_must' => [],
        ])->setRules([
            'must'          => 'array',
            'not_must'      => '!array',
            'required_must' => 'required|array',
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }
}
