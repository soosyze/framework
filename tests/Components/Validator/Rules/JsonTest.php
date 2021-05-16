<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class JsonTest extends Rule
{
    public function testJson(): void
    {
        $json = '{"key":"value"}';

        $this->object->setInputs([
            'must'              => $json,
            'not_must'          => 'no json',
            'required_must'     => $json,
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'json',
            'not_must'          => '!json',
            'required_must'     => 'required|json',
            'not_required_must' => '!required|json'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'no json',
            'not_must' => $json
        ])->setRules([
            'must'     => 'json',
            'not_must' => '!json'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
