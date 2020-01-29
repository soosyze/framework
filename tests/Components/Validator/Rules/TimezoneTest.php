<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class TimezoneTest extends Rule
{
    public function testTimezone()
    {
        $this->object->setInputs([
            'must'              => 'Europe/Paris',
            'not_must'          => 1
        ])->setRules([
            'must'              => 'timezone',
            'not_must'           => '!timezone'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 1,
            'not_must' => 'Europe/Paris'
        ])->setRules([
            'must'     => 'timezone',
            'not_must' => '!timezone'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
