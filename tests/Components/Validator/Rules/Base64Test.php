<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class Base64Test extends Rule
{
    public function testBase64()
    {
        $this->object->setInputs([
            'must'     => $this->getBase64(),
            'not_must' => 'no base64'
        ])->setRules([
            'must'     => 'base64',
            'not_must' => '!base64'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'no base64',
            'not_must' => $this->getBase64()
        ])->setRules([
            'must'     => 'base64',
            'not_must' => '!base64'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    protected function getBase64()
    {
        return 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
            . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
            . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
            . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
    }
}
