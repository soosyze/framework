<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class AlphaNumTest extends Rule
{
    public function testAlphaNum()
    {
        $this->object->setInputs([
            'must'              => 'hello2000',
            'not_must'          => 'hello&2000@',
            'required_must'     => 'hello2000',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'alpha_num',
            'not_must'          => '!alpha_num',
            'required_must'     => 'required|alpha_num',
            'not_required_must' => '!required|alpha_num'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => [],
            'not_must' => 'hello2000'
        ])->setRules([
            'must'     => 'alpha_num',
            'not_must' => '!alpha_num'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testAlphaNumText()
    {
        $this->object->setInputs([
            'must'              => 'hello2000.',
            'not_must'          => 'hello&2000@',
            'required_must'     => 'hello2000!',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'alpha_num_text',
            'not_must'          => '!alpha_num_text',
            'required_must'     => 'required|alpha_num_text',
            'not_required_must' => '!required|alpha_num_text'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'hello&2000@',
            'not_must' => 'hello2000.'
        ])->setRules([
            'must'     => 'alpha_num_text',
            'not_must' => '!alpha_num_text'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
