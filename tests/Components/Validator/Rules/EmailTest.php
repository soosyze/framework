<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class EmailTest extends Rule
{
    public function testEmail()
    {
        $this->object->setInputs([
            'must'              => 'my-mail@mail.fr',
            'not_must'          => 'not mail',
            'required_must'     => 'my-mail@mail.fr',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'email',
            'not_must'          => '!email',
            'required_must'     => 'required|email',
            'not_required_must' => '!required|email'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not mail',
            'not_must' => 'my-mail@mail.fr'
        ])->setRules([
            'must'     => 'email',
            'not_must' => '!email'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
