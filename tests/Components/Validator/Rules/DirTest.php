<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class DirTest extends Rule
{
    public function testDir(): void
    {
        $this->object->setInputs([
            'must'              => __DIR__,
            'not_must'          => 'not dir',
            'required_must'     => __DIR__,
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'dir',
            'not_must'          => '!dir',
            'required_must'     => 'required|dir',
            'not_required_must' => '!required|dir'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not dir',
            'not_must' => __DIR__
        ])->setRules([
            'must'     => 'dir',
            'not_must' => '!dir'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
