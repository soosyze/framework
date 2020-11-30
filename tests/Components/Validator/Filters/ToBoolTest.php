<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToBoolTest extends Filter
{
    public function testToBool()
    {
        $this->object->setInputs([
            /* True */
            'true'           => true,
            'true_txt'       => 'true',
            'true_one'       => 1,
            'true_one_txt'   => '1',
            'true_on'        => 'on',
            'true_yes'       => 'yes',
            /* False */
            'false'          => false,
            'false_text'     => 'false',
            'false_zero'     => 0,
            'false_zero_txt' => '0',
            'false_off'      => 'off',
            'false_no'       => 'no',
            'false_void'     => ''
        ])->setRules([
            /* True */
            'true'           => 'to_bool',
            'true_txt'       => 'to_bool',
            'true_one'       => 'to_bool',
            'true_one_txt'   => 'to_bool',
            'true_on'        => 'to_bool',
            'true_yes'       => 'to_bool',
            /* False */
            'false'          => 'to_bool',
            'false_txt'      => 'to_bool',
            'false_zero'     => 'to_bool',
            'false_zero_txt' => 'to_bool',
            'false_off'      => 'to_bool',
            'false_no'       => 'to_bool',
            'false_void'     => 'to_bool'
        ])->isValid();

        /* True */
        $this->assertTrue(is_bool($this->object->getInput('true')));
        $this->assertTrue(is_bool($this->object->getInput('true_txt')));
        $this->assertTrue(is_bool($this->object->getInput('true_one')));
        $this->assertTrue(is_bool($this->object->getInput('true_one_txt')));
        $this->assertTrue(is_bool($this->object->getInput('true_on')));
        $this->assertTrue(is_bool($this->object->getInput('true_yes')));
        /* False */
        $this->assertTrue(is_bool($this->object->getInput('false')));
        $this->assertTrue(is_bool($this->object->getInput('false_txt')));
        $this->assertTrue(is_bool($this->object->getInput('false_zero')));
        $this->assertTrue(is_bool($this->object->getInput('false_zero_txt')));
        $this->assertTrue(is_bool($this->object->getInput('false_off')));
        $this->assertTrue(is_bool($this->object->getInput('false_no')));
        $this->assertTrue(is_bool($this->object->getInput('false_void')));
    }

    /**
     * @expectedException \Exception
     */
    public function testToBoolException()
    {
        $this->object
            ->addInput('field', 'error')
            ->addRule('field', 'to_bool')
            ->isValid();
    }
}
