<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class DateTest extends Rule
{
    public function testDate()
    {
        $this->object->setInputs([
            'must'              => '10/01/1994',
            'not_must'          => 'not date',
            'required_must'     => '10/01/1994',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'date',
            'not_must'          => '!date',
            'required_must'     => 'required|date',
            'not_required_must' => '!required|date'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not date',
            'not_must' => '10/01/1994'
        ])->setRules([
            'date'     => 'date',
            'not_must' => '!date'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testDateFormat()
    {
        $this->object->setInputs([
            'must'              => '20/01/1994',
            'not_must'          => '1994/01/20',
            'required_must'     => '20/01/1994',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'date_format:j/m/Y',
            'not_must'          => '!date_format:j/m/Y',
            'required_must'     => 'required|date_format:j/m/Y',
            'not_required_must' => '!required|date_format:j/m/Y'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'error'    => 'not date',
            'must'     => '1994/10/01',
            'not_must' => '10/01/1994'
        ])->setRules([
            'error'    => 'date_format:j/m/Y',
            'must'     => 'date_format:j/m/Y',
            'not_must' => '!date_format:j/m/Y'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    public function testDateAfter()
    {
        $this->object->setInputs([
            'must'              => '10/02/1994',
            'not_must'          => '09/01/1994',
            'required_must'     => '10/02/1994',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'date_after:10/01/1994',
            'not_must'          => '!date_after:10/01/1994',
            'required_must'     => 'required|date_after:10/01/1994',
            'not_required_must' => '!required|date_after:10/01/1994'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'error'    => 'not date',
            'must'     => '10/01/1994',
            'not_must' => '10/02/1994'
        ])->setRules([
            'error'    => 'date_after:10/01/1994',
            'must'     => 'date_after:10/01/1994',
            'not_must' => '!date_after:10/01/1994'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    /**
     * @expectedException \Exception
     */
    public function testDateAfterException()
    {
        $this->object
            ->addInput('field', 'not date')
            ->addRule('field', 'date_after:error')
            ->isValid();
    }

    public function testDateAfterOrEqual()
    {
        $this->object->setInputs([
            'must'              => '10/01/1994',
            'not_must'          => '09/01/1994',
            'required_must'     => '10/02/1994',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'date_after_or_equal:10/01/1994',
            'not_must'          => '!date_after_or_equal:10/01/1994',
            'required_must'     => 'required|date_after_or_equal:10/01/1994',
            'not_required_must' => '!required|date_after_or_equal:10/01/1994'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => '09/01/1994',
            'not_must' => '10/01/1994'
        ])->setRules([
            'must'     => 'date_after_or_equal:10/01/1994',
            'not_must' => '!date_after_or_equal:10/01/1994'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testDateBefore()
    {
        $this->object->setInputs([
            'must'              => '09/01/1994',
            'not_must'          => '10/01/1994',
            'required_must'     => '09/01/1994',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'date_before:10/01/1994',
            'not_must'          => '!date_before:10/01/1994',
            'required_must'     => 'required|date_before:10/01/1994',
            'not_required_must' => '!required|date_before:10/01/1994'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'error'    => 'not date',
            'must'     => '11/01/1994',
            'not_must' => '09/01/1994'
        ])->setRules([
            'error'    => 'date_before:10/01/1994',
            'must'     => 'date_before:10/01/1994',
            'not_must' => '!date_before:10/01/1994'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    /**
     * @expectedException \Exception
     */
    public function testDateBeforeException()
    {
        $this->object
            ->addInput('field', 'not date')
            ->addRule('field', 'date_before:error')
            ->isValid();
    }

    public function testDateBeforeOrEqual()
    {
        $this->object->setInputs([
            'must'              => '10/01/1994',
            'not_must'          => '10/01/1994',
            'required_must'     => '10/01/1994',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'date_before_or_equal:10/01/1994',
            'not_must'          => '!date_before_or_equal:09/01/1994',
            'required_must'     => 'required|date_before_or_equal:10/01/1994',
            'not_required_must' => '!required|date_before_or_equal:09/01/1994'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => '10/01/1994',
            'not_must' => '10/01/1994'
        ])->setRules([
            'must'     => 'date_before_or_equal:09/01/1994',
            'not_must' => '!date_before_or_equal:10/01/1994'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
