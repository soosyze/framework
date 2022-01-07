<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToLtrimTest extends Filter
{
    public function testToLtrim(): void
    {
        $this->object->setInputs([
            'start'     => '  test',
            'end'       => 'test  ',
            'start_end' => '  test  ',
            'custom'    => '$test'
        ])->setRules([
            'start'     => 'to_ltrim',
            'end'       => 'to_ltrim',
            'start_end' => 'to_ltrim',
            'custom'    => 'to_ltrim:$'
        ])->isValid();

        $this->assertEquals('test', $this->object->getInput('start'));
        $this->assertEquals('test  ', $this->object->getInput('end'));
        $this->assertEquals('test  ', $this->object->getInput('start_end'));
        $this->assertEquals('test', $this->object->getInput('custom'));
    }

    public function testToLtrimException(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('field', 1)
            ->addRule('field', 'to_ltrim')
            ->isValid();
    }

    public function testExceptionArg(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->object
            ->addInput('args', 1)
            ->addInput('field', '1')
            ->addRule('field', 'to_ltrim:@args')
            ->isValid();
    }
}
