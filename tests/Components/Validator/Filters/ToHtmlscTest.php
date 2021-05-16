<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToHtmlscTest extends Filter
{
    public function testToHtmlsc(): void
    {
        $this->object
            ->addInput('field', '<p>bonjour</p>')
            ->addRule('field', 'to_htmlsc:<p>')
            ->isValid();

        $this->assertEquals('&lt;p&gt;bonjour&lt;/p&gt;', $this->object->getInput('field'));
    }

    public function testToHtmlscException(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('field', 1)
            ->addRule('field', 'to_htmlsc:<p>')
            ->isValid();
    }
}
