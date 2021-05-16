<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class InstanceTest extends Rule
{
    public function testInstance(): void
    {
        $this->object->setInputs([
            'must'     => new \stdClass(),
            'not_must' => null
        ])->setRules([
            'must'     => 'instanceof:\stdClass',
            'not_must' => '!instanceof:\stdClass'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => null,
            'not_must' => new \stdClass()
        ])->setRules([
            'must'     => 'instanceof:\stdClass',
            'not_must' => '!instanceof:\stdClass'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
