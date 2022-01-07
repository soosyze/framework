<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class InArrayTest extends Rule
{
    public function testInArray(): void
    {
        $this->object->setInputs([
            'must'              => '1',
            'must_args'         => '1',
            'not_must'          => '1',
            'required_must'     => '1',
            'not_required_must' => '',
            'args'              => [ 1, 2, 3, 4, 5 ]
        ])->setRules([
            'must'              => 'inarray:1,2,3,4,5',
            'must_args'         => 'inarray:@args',
            'not_must'          => '!inarray:2,3,4,5',
            'required_must'     => 'required|inarray:1,2,3,4,5',
            'not_required_must' => '!required|inarray:1,2,3,4,5'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => '1',
            'not_must' => '1'
        ])->setRules([
            'must'     => 'inarray:2,3,4,5',
            'not_must' => '!inarray:1,2,3,4,5'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testExceptionArg(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The arguments must be a string or array.');
        $this->object
            ->addInput('args', 1)
            ->addInput('field', '1')
            ->addRule('field', 'inarray:@args')
            ->isValid();
    }
}
