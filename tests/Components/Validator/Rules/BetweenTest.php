<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class BetweenTest extends Rule
{
    public function testBetween()
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
     * @expectedException \Exception
     */
    public function testBetweenMissingException()
    {
        $this->object
            ->addInput('field', 4)
            ->addRule('field', 'between')
            ->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testBetweenExceptionTypeMin()
    {
        $this->object
            ->addInput('field', 4)
            ->addRule('field', 'between:error,5')
            ->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testBetweenExceptionTypeMax()
    {
        $this->object
            ->addInput('field', 4)
            ->addRule('field', 'between:1,error')
            ->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testBetweenMinUpperMax()
    {
        $this->object
            ->addInput('field', 4)
            ->addRule('field', 'between:10,1')
            ->isValid();
    }

    public function testBetweenNumeric()
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
}
