<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class RequiredWithAllTest extends Rule
{
    public function testRequiredWith()
    {
        $this->object->setInputs([
            'field_1' => '',
            'field_2' => ''
        ])->setRules([
            'field_1'  => '!required',
            'field_2'  => '!required',
            'required' => 'required_with_all:field_1,field_2'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_1' => '',
            'field_2' => 1
        ])->setRules([
            'field_1'  => '!required',
            'field_2'  => 'int',
            'required' => 'required_with_all:field_1,field_2'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_1' => 1,
            'field_2' => 1
        ])->setRules([
            'field_1'  => 'int',
            'field_2'  => 'int',
            'required' => 'required_with_all:field_1,field_2'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());

        $this->object->setInputs([
            'field_1' => 1,
            'field_2' => 1
        ])->setRules([
            'field_1'  => 'int',
            'field_2'  => 'int',
            'required' => '!required_with_all:field_1,field_2'
        ]);

        $this->assertTrue($this->object->isValid());
    }

    /**
     * @expectedException \Exception
     */
    public function testRequiredWithException()
    {
        $this->object->addRule('field', 'required_with_all:error')
            ->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testRequiredWithVoidException()
    {
        $this->object->addRule('field', 'required_with_all:')
            ->isValid();
    }
}
