<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class MaxTest extends Rule
{
    public function testMax()
    {
        $this->object->setInputs([
            /* Text */
            'text'              => 'Lorem',
            'not_text'          => 'Lorem ipsum',
            'text_required'     => 'Lore',
            'text_not_required' => '',
            /* Integer */
            'int'               => 5,
            'not_int'           => 6,
            'int_required'      => 5,
            'int_not_required'  => '',
            /* Array */
            'array'             => [ 1, 2, 3, 4, 5 ],
            'not_array'         => [ 1, 2, 3, 4, 5, 6 ]
        ])->setRules([
            /* Text */
            'text'              => 'max:5',
            'not_text'          => '!max:5',
            'text_required'     => 'required|max:5',
            'text_not_required' => '!required|max:5',
            /* Integer */
            'int'               => 'max:5',
            'not_int'           => '!max:5',
            'int_required'      => 'required|max:5',
            'int_not_required'  => '!required|max:5',
            /* Array */
            'array'             => 'max:5',
            'not_array'         => '!max:5'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'Lorem ipsum',
            'not_must' => 'Lorem',
            'size'     => new \stdClass()
        ])->setRules([
            'must'     => 'max:5',
            'not_must' => '!max:5',
            'size'     => '!max:5'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    public function testMaxNumeric()
    {
        $this->object->setInputs([
            /* Integer */
            'int'              => 5,
            'not_int'          => 6,
            'int_required'     => 5,
            'int_not_required' => '',
            /* Numeric */
            'numeric'          => '5',
            'not_numeric'      => '6',
            'numeric_required' => '5'
        ])->setRules([
            /* Integer */
            'int'              => 'max_numeric:5',
            'not_int'          => '!max_numeric:5',
            'int_required'     => 'required|max_numeric:5',
            'int_not_required' => '!required|max_numeric:5',
            /* Numeric */
            'numeric'          => 'max_numeric:5',
            'not_numeric'      => '!max_numeric:5',
            'numeric_required' => 'required|max_numeric:5'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'Lorem ipsum',
            'not_must' => 'Lore'
        ])->setRules([
            'must'     => 'max_numeric:5',
            'not_must' => '!max_numeric:5'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    /**
     * @expectedException \Exception
     */
    public function testMaxExceptionMin()
    {
        $this->object
            ->addInput('field', 4)
            ->addRule('field', 'max:error')
            ->isValid();
    }
}
