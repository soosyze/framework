<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class ClassExistsTest extends Rule
{
    public function testClass()
    {
        $this->object->setInputs([
            'must'              => '\StdClass',
            'not_must'          => 'not class',
            'required_must'     => '\StdClass',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'class_exists',
            'not_must'          => '!class_exists',
            'required_must'     => 'required|class_exists',
            'not_required_must' => '!required|class_exists'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not class',
            'not_must' => '\StdClass'
        ])->setRules([
            'must'     => 'class_exists',
            'not_must' => '!class_exists'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
