<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToTrimTest extends Filter
{
    public function testToTrim(): void
    {
        $this->object->setInputs([
            'start'     => '  test',
            'end'       => 'test  ',
            'start_end' => '  test  ',
            'custom'    => '$test$'
        ])->setRules([
            'start'     => 'to_trim',
            'end'       => 'to_trim',
            'start_end' => 'to_trim',
            'custom'    => 'to_trim:$'
        ])->isValid();

        $this->assertEquals('test', $this->object->getInput('start'));
        $this->assertEquals('test', $this->object->getInput('end'));
        $this->assertEquals('test', $this->object->getInput('start_end'));
        $this->assertEquals('test', $this->object->getInput('custom'));
    }

    public function testToTrimException(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('field', 1)
            ->addRule('field', 'to_trim')
            ->isValid();
    }
}
