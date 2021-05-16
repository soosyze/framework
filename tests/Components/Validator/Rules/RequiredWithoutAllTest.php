<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class RequiredWithoutAllTest extends Rule
{
    public function testRequiredWithout(): void
    {
        $this->object->setInputs([
            'field_1' => 1,
            'field_2' => 1
        ])->setRules([
            'required' => 'required_without_all:field_1,field_2'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_1' => '',
            'field_2' => 1
        ])->setRules([
            'required' => 'required_without_all:field_1,field_2'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_1' => '',
            'field_2' => ''
        ])->setRules([
            'required' => 'required_without_all:field_1,field_2'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }

    public function testRequiredWithoutAllIntegration(): void
    {
        $this->object->setInputs([
            'field_1' => '',
            'field_2' => ''
        ])->setRules([
            'required' => '!required_without_all:field_1,field_2|int'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_1' => 1,
            'field_2' => ''
        ])->setRules([
            'required' => '!required_without_all:field_1,field_2|int'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());

        $this->object->setInputs([
            'field_1'  => 1,
            'field_2'  => 1,
            'required' => 1
        ])->setRules([
            'required' => 'required_without_all:field_1,field_2|!int'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }

    public function testRequiredWithoutException(): void
    {
        $this->expectException(\Exception::class);
        $this->object->addRule('field', 'required_without_all:error')->isValid();
    }

    public function testRequiredWithoutVoidException(): void
    {
        $this->expectException(\Exception::class);
        $this->object->addRule('field', 'required_without_all:')->isValid();
    }
}
