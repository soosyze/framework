<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class RequiredWithoutTest extends Rule
{
    public function testRequiredWithout()
    {
        $this->object->setInputs([
            'field_1' => 1,
            'field_2' => 1
        ])->setRules([
            'field_1'  => 'int',
            'field_2'  => 'int',
            'required' => 'required_without:field_1,field_2'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_1' => '',
            'field_2' => 1
        ])->setRules([
            'field_1'  => '!required',
            'field_2'  => 'int',
            'required' => 'required_without:field_1,field_2'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());

        $this->object->setInputs([
            'field_1' => '',
            'field_2' => ''
        ])->setRules([
            'field_1'  => '!required',
            'field_2'  => '!required',
            'required' => 'required_without:field_1,field_2'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());

        $this->object->setInputs([
            'field_1' => '',
            'field_2' => ''
        ])->setRules([
            'field_1'  => '!required',
            'field_2'  => '!required',
            'required' => '!required_without:field_1,field_2'
        ]);

        $this->assertTrue($this->object->isValid());
    }

    /**
     * @expectedException \Exception
     */
    public function testRequiredWithoutException()
    {
        $this->object->addRule('field', 'required_without:error')->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testRequiredWithoutVoidException()
    {
        $this->object->addRule('field', 'required_without:')->isValid();
    }
}
