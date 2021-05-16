<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToStripTagsTest extends Filter
{
    public function testToStripTags(): void
    {
        $this->object
            ->addInput('field', '<p>bonjour <a href="#">lien</a></p>')
            ->addRule('field', 'to_striptags:<p>')
            ->isValid();

        $this->assertEquals('<p>bonjour lien</p>', $this->object->getInput('field'));
    }

    public function testToStripTagsException(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('field', 1)
            ->addRule('field', 'to_striptags:<p>')
            ->isValid();
    }
}
