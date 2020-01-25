<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class AcceptedTest extends Rule
{
    public function testAccepted()
    {
        $this->object->setInputs([
            'true'      => true,
            'true_text' => 'true',
            'one'       => 1,
            'one_text'  => '1',
            'on'        => 'on',
            'yes'       => 'yes'
        ])->setRules([
            'true'      => 'accepted',
            'true_text' => 'accepted',
            'one'       => 'accepted',
            'one_text'  => 'accepted',
            'on'        => 'accepted',
            'yes'       => 'accepted'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not bool',
            'not_must' => true
        ])->setRules([
            'must'     => 'accepted',
            'not_must' => '!accepted'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
