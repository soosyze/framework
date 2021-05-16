<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class UuidTest extends Rule
{
    /**
     * @var string
     */
    protected $uuid = 'e325f454-f722-43fd-99d4-c06a1eee1abf';

    public function testUuid(): void
    {
        $this->object->setInputs([
            'must'              => $this->uuid,
            'not_must'          => 'not uuid',
            'required_must'     => $this->uuid,
            'not_required_must' => ''
        ])->setRules([
            'must'              => 'uuid',
            'not_url'           => '!uuid',
            'required_must'     => 'required|uuid',
            'not_required_must' => '!required|uuid'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'not uuid',
            'not_must' => $this->uuid,
        ])->setRules([
            'must'     => 'uuid',
            'not_must' => '!uuid'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
