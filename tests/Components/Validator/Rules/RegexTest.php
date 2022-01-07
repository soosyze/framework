<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class RegexTest extends Rule
{
    public function testRegex(): void
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

    public function testExceptionArgMin(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The argument must be a string.');
        $this->object
            ->addInput('args', 1)
            ->addInput('field', '1')
            ->addRule('field', 'regex:@args')
            ->isValid();
    }
}
