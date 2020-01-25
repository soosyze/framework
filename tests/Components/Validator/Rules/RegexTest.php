<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class RegexTest extends Rule
{
    public function testRegex()
    {
        $this->object->setInputs([
            'must'              => 'hello world',
            'not_must'          => 'hello world',
            'required_must'     => 'hello world',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'regex:/^h.*/',
            'not_must'          => '!regex:/^w.*/',
            'required_must'     => 'required|regex:/^h.*/',
            'not_required_must' => '!required|regex:/^h.*/'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'hello world',
            'not_must' => 'hello world'
        ])->setRules([
            'must'     => 'regex:/^w.*/',
            'not_must' => '!regex:/^h.*/'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
