<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class RessourceTest extends Rule
{
    use \Soosyze\Tests\Traits\ResourceTrait;

    public function testAccepted(): void
    {
        $stream = $this->streamFactory('test', 'r+');

        $this->object->setInputs([
            'must'     => $stream,
            'not_must' => 'no_ressource'
        ])->setRules([
            'must'     => 'ressource',
            'not_must' => '!ressource'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'no_ressource',
            'not_must' => $stream
        ])->setRules([
            'must'     => 'ressource',
            'not_must' => '!ressource'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
        fclose($stream);
    }
}
