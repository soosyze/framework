<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class BetweenTest extends Rule
{
    public function testBetween(): void
    {
        $this->object->setInputs([
            /* Text */
            'text_min'           => 'Lorem',
            'text_max'           => 'Lorem ipsum doe',
            'not_text'           => 'Lore',
            'text_required'      => 'Lorem ipsum',
            'text_not_required'  => '',
            /* Integer */
            'int_min'            => 5,
            'int_max'            => 15,
            'not_int'            => 16,
            'int_min_required'   => 5,
            'int_max_required'   => 15,
            'int_not_required'   => '',
            /* Array */
            'array_min'          => [ 1, 2, 3, 4, 5 ],
            'array_max'          => [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ],
            'not_array'          => [ 1, 2, 3, 4 ],
            'array_min_required' => [ 1, 2, 3, 4, 5 ],
            'array_max_required' => [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ],
            'array_not_required' => ''
        ])->setRules([
            /* Text */
            'text_min'           => 'between:5,15',
            'text_max'           => 'between:5,15',
            'not_text'           => '!between:5,15',
            'text_required'      => 'required|between:5,15',
            'text_not_required'  => '!required|between:5,15',
            /* Integer */
            'int_min'            => 'between:5,15',
            'int_max'            => 'between:5,15',
            'not_int'            => '!between:5,15',
            'int_min_required'   => 'required|between:5,15',
            'int_max_required'   => 'required|between:5,15',
            'int_not_required'   => '!required|between:5,15',
            /* Array */
            'array_min'          => 'between:5,10',
            'array_max'          => 'between:5,10',
            'not_array'          => '!between:5,10',
            'array_min_required' => 'required|between:5,15',
            'array_max_required' => 'required|between:5,15',
            'array_not_required' => '!required|between:5,15'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            /* Text */
            'must'     => 'Lor',
            'not_must' => 'Lorem ip',
            'error'    => new \stdClass()
        ])->setRules([
            /* Text */
            'must'     => 'between:5,10',
            'not_must' => '!between:5,10',
            'error'    => '!between:5,10'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    /**
     * @dataProvider providerBetweenException
     */
    public function testBetweenException(string $rule): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('field', 4)
            ->addRule('field', $rule)
            ->isValid();
    }

    public function providerBetweenException(): \Generator
    {
        yield [ 'between' ];
        yield [ 'between:error,5' ];
        yield [ 'between:1,error' ];
        yield [ 'between:10,1' ];
    }

    public function testBetweenNumeric(): void
    {
        $this->object->setInputs([
            /* Integer */
            'int_min'          => 5,
            'int_max'          => 15,
            'not_int'          => 16,
            'int_min_required' => 5,
            'int_max_required' => 15,
            'int_not_required' => '',
        ])->setRules([
            /* Text */
            'int_min'          => 'between_numeric:5,15',
            'int_max'          => 'between_numeric:5,15',
            'not_int'          => '!between_numeric:5,15',
            'int_min_required' => 'required|between_numeric:5,15',
            'int_max_required' => 'required|between_numeric:5,15',
            'int_not_required' => '!required|between_numeric:5,15',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            /* Text */
            'must'     => 'Lor',
            'not_must' => 'Lorem ip',
            'error'    => new \stdClass()
        ])->setRules([
            /* Text */
            'must'     => 'between_numeric:5,10',
            'not_must' => '!between_numeric:5,10',
            'error'    => '!between_numeric:5,10'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    public function testExceptionArg(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The comparisons arguments must be a string.');
        $this->object
            ->addInput('args', 1)
            ->addInput('field', '1')
            ->addRule('field', 'between_numeric:@args')
            ->isValid();
    }
}
