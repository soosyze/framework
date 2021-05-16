<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class StringTest extends Rule
{
    /**
     * @var string
     */
    public $heredoc = <<<EOT
bar
EOT;

    public function testString(): void
    {
        $this->object->setInputs([
            'single_quotes' => 'Lorem ipsum',
            'double_quotes' => 'Lorem ipsum',
            'heredoc'       => $this->heredoc,
            'int'           => 10,
            'float'         => 10.1,
            'array'         => [ 1, 2 ]
        ])->setRules([
            'single_quotes' => 'string',
            'double_quotes' => 'string',
            'heredoc'       => 'string',
            'int'           => '!string',
            'float'         => '!string',
            'array'         => '!string'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'          => 10,
            'required_must' => '',
            'int'           => 10,
            'float'         => 10.1,
            'array'         => [ 1, 2 ],
            'not_must'      => 'test'
        ])->setRules([
            'must'          => 'string',
            'required_must' => 'required|string',
            'int'           => 'string',
            'float'         => 'string',
            'array'         => 'string',
            'not_must'      => '!string'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(6, $this->object->getErrors());
    }
}
