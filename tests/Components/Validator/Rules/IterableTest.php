<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class IterableTest extends Rule
{
    public function testIterable(): void
    {
        $this->object->setInputs([
            'must'             => [ 0, 1, 2 ],
            'must_func_array'  => $this->mus_func_array(),
            'must_traversable' => new \ArrayIterator([ 1, 2, 3 ]),
            'not_must'         => 'hello'
        ])->setRules([
            'must'             => 'iterable',
            'must_func_array'  => 'iterable',
            'must_traversable' => 'iterable',
            'not_must'         => '!iterable'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not array',
            'not_must' => [ 0, 1, 2 ]
        ])->setRules([
            'must'     => 'iterable',
            'not_must' => '!iterable'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    protected function mus_func_array(): array
    {
        return [ 0, 1, 2 ];
    }
}
