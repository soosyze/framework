<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToRtrimTest extends Filter
{
    public function testToRtrim()
    {
        $this->object->setInputs([
            'start'     => '  test',
            'end'       => 'test  ',
            'start_end' => '  test  '
        ])->setRules([
            'start'     => 'to_rtrim',
            'end'       => 'to_rtrim',
            'start_end' => 'to_rtrim'
        ])->isValid();

        $this->assertEquals('  test', $this->object->getInput('start'));
        $this->assertEquals('test', $this->object->getInput('end'));
        $this->assertEquals('  test', $this->object->getInput('start_end'));
    }
}
