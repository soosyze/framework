<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToHtmlscTest extends Filter
{
    public function testToHtmlsc()
    {
        $this->object
            ->addInput('field', '<p>bonjour</p>')
            ->addRule('field', 'to_htmlsc:<p>')
            ->isValid();

        $this->assertAttributeSame([ 'field' => '&lt;p&gt;bonjour&lt;/p&gt;' ], 'inputs', $this->object);
    }

    /**
     * @expectedException \Exception
     */
    public function testToHtmlscException()
    {
        $this->object
            ->addInput('field', 1)
            ->addRule('field', 'to_htmlsc:<p>')
            ->isValid();
    }
}
