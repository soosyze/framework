<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class ColorHexTest extends Rule
{
    public function testColorHex(): void
    {
        $this->object->setInputs([
            'color3_default'     => '#FFF',
            'color6_default'     => '#FFFFFF',
            'color3'             => '#FFF',
            'not_color3'         => 'not color',
            'color6'             => '#FFFFFF',
            'not_color'          => 'not color',
            'color_required'     => '#FFF',
            'color_not_required' => ''
        ])->setRules([
            'color3_default'     => 'colorhex',
            'color6_default'     => 'colorhex',
            'color3'             => 'colorhex:3',
            'not_color3'         => '!colorhex:3',
            'color6'             => 'colorhex:6',
            'not_color'          => '!colorhex:6',
            'color_required'     => 'required|colorhex',
            'color_not_required' => '!required|colorhex'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'color6_symbol' => 'FFFFFF',
            'color6_letter' => '#FFFFFM',
            'color6_length' => '#FFFFFFF',
            'color3_symbol' => 'FFF',
            'color3_letter' => '#FFM',
            'color3_length' => '#FFFF'
        ])->setRules([
            'color6_symbol' => 'colorhex:6',
            'color6_letter' => 'colorhex:6',
            'color6_length' => 'colorhex:6',
            'color3_symbol' => 'colorhex:3',
            'color3_letter' => 'colorhex:3',
            'color3_length' => 'colorhex:3'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(6, $this->object->getErrors());
    }

    public function testColorHexException(): void
    {
        $this->expectException(\Exception::class);
        $this->object->addInput('field', '#FFF')
            ->addRule('field', 'colorhex:4')
            ->isValid();
    }
}
