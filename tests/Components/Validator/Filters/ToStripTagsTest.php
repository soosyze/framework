<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToStripTagsTest extends Filter
{
    public function testToStripTags()
    {
        $this->object
            ->addInput('field', '<p>bonjour <a href="#">lien</a></p>')
            ->addRule('field', 'to_striptags:<p>')
            ->isValid();

        $this->assertAttributeSame([ 'field' => '<p>bonjour lien</p>' ], 'inputs', $this->object);
    }

    /**
     * @expectedException \Exception
     */
    public function testToStripTagsException()
    {
        $this->object
            ->addInput('field', 1)
            ->addRule('field', 'to_striptags:<p>')
            ->isValid();
    }
}
