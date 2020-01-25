<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class FontAwesomeTest extends Rule
{
    public function testFontAwesome()
    {
        $this->object->setInputs([
            'field_font'     => 'fa fa-user',
            'field_font_r'   => 'far fa-user',
            'field_font_rs'  => 'fas fa-user',
            'field_not_font' => 'not font'
        ])->setRules([
            'field_font'     => 'fontawesome',
            'field_font_r'   => 'fontawesome:regular',
            'field_font_rs'  => 'fontawesome:regular,solid',
            'field_not_font' => '!fontawesome'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_font'     => 'not font',
            'field_not_font' => 'fa fa-user'
        ])->setRules([
            'field_font'     => 'fontawesome',
            'field_not_font' => '!fontawesome'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
