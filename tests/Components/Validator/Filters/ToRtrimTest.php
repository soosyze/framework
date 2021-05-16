<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToRtrimTest extends Filter
{
    public function testToRtrim(): void
    {
        $this->object->setInputs([
            'start'     => '  test',
            'end'       => 'test  ',
            'start_end' => '  test  ',
            'custom'    => 'test$'
        ])->setRules([
            'start'     => 'to_rtrim',
            'end'       => 'to_rtrim',
            'start_end' => 'to_rtrim',
            'custom'    => 'to_rtrim:$'
        ])->isValid();

        $this->assertEquals('  test', $this->object->getInput('start'));
        $this->assertEquals('test', $this->object->getInput('end'));
        $this->assertEquals('  test', $this->object->getInput('start_end'));
        $this->assertEquals('test', $this->object->getInput('custom'));
    }

    public function testToRtrimException(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('field', 1)
            ->addRule('field', 'to_rtrim')
            ->isValid();
    }
}
