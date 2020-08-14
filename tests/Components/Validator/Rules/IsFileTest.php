<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class IsFileTest extends Rule
{
    public function testIsFile()
    {
        $this->object->setInputs([
            'must'              => __DIR__ . '/IsFileTest.php',
            'not_must'          => 'not file',
            'required_must'     => __DIR__ . '/IsFileTest.php',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'is_file',
            'not_must'          => '!is_file',
            'required_must'     => 'required|is_file',
            'not_required_must' => '!required|is_file'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not file',
            'not_must' => __DIR__ . '/IsFileTest.php',
        ])->setRules([
            'must'     => 'is_file',
            'not_must' => '!is_file'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
