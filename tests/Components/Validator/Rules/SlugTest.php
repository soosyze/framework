<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class SlugTest extends Rule
{
    public function testSlug(): void
    {
        $this->object->setInputs([
            'must'              => 'hello-world',
            'not_must'          => 'hello world',
            'required_must'     => 'hello_world',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'slug',
            'not_must'          => '!slug',
            'required_must'     => 'required|slug',
            'not_required_must' => '!required|slug'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'hello world',
            'not_must' => 'hello-world'
        ])->setRules([
            'must'     => 'slug',
            'not_must' => '!slug'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
