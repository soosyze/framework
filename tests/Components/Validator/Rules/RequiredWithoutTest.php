<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class RequiredWithoutTest extends Rule
{
    public function testRequiredWithout(): void
    {
        $this->object->setInputs([
            'field_1' => 1,
            'field_2' => 1
        ])->setRules([
            'required' => 'required_without:field_1,field_2'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_1' => '',
            'field_2' => 1
        ])->setRules([
            'required' => 'required_without:field_1,field_2'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());

        $this->object->setInputs([
            'field_1' => '',
            'field_2' => ''
        ])->setRules([
            'required' => 'required_without:field_1,field_2'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }

    public function testRequiredWithoutIntegration(): void
    {
        $this->object->setInputs([
            'field_1' => '',
            'field_2' => ''
        ])->setRules([
            'required' => '!required_without:field_1,field_2|int'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_1' => 1,
            'field_2' => ''
        ])->setRules([
            'required' => '!required_without:field_1,field_2|int'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_1'  => 1,
            'field_2'  => 1,
            'required' => 1
        ])->setRules([
            'required' => 'required_without:field_1,field_2|!int'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }

    public function testRequiredWithoutException(): void
    {
        $this->expectException(\Exception::class);
        $this->object->addRule('field', 'required_without:error')->isValid();
    }

    public function testRequiredWithoutVoidException(): void
    {
        $this->expectException(\Exception::class);
        $this->object->addRule('field', 'required_without:')->isValid();
    }

    public function testExceptionArgMin(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The argument must be a string.');
        $this->object
            ->addInput('args', 1)
            ->addInput('field', '1')
            ->addRule('field', 'required_without:@args')
            ->isValid();
    }
}
