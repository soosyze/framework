<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class UrlTest extends Rule
{
    public function testUrl()
    {
        $this->object->setInputs([
            'must'              => 'http://localhost.fr',
            'not_must'          => 'not url',
            'required_must'     => 'http://localhost.fr',
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'url',
            'not_url'           => '!url',
            'required_must'     => 'required|url',
            'not_required_must' => '!required|url'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not url',
            'not_must' => 'http://localhost.fr'
        ])->setRules([
            'must'     => 'url',
            'not_must' => '!url'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
