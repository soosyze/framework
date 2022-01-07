<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class MinTest extends Rule
{
    use \Soosyze\Tests\Traits\ResourceTrait;

    public function testMin(): void
    {
        $stream = $this->streamFactory('test content', 'r+');

        $this->object->setInputs([
            /* Text */
            'text'              => 'Lorem ipsum',
            'not_text'          => 'Lore',
            'text_required'     => 'Lorem ipsum',
            'text_not_required' => '',
            /* Integer */
            'int'               => 5,
            'not_int'           => 4,
            'int_required'      => 5,
            'int_not_required'  => '',
            /* Float */
            'float'             => 5.5,
            'not_float'         => 4.5,
            'float_required'    => 5.5,
            /* Tableau */
            'array'             => [ 1, 2, 3, 4, 5 ],
            'not_array'         => [ 1, 2, 3, 4 ],
            /* Ressource */
            'ressource'         => $stream,
            /* Object */
            'object'            => new ObjectTest
        ])->setRules([
            /* Text */
            'text'              => 'min:5',
            'not_text'          => '!min:5',
            'text_required'     => 'required|min:5',
            'text_not_required' => '!required|min:5',
            /* Integer */
            'int'               => 'min:5',
            'not_int'           => '!min:5',
            'int_required'      => 'required|min:5',
            'int_not_required'  => '!required|min:5',
            /* Float */
            'float'             => 'min:5.5',
            'not_float'         => '!min:5.0',
            'float_required'    => 'required|min:5.5',
            /* Tableau */
            'array'             => 'min:5',
            'not_array'         => '!min:5',
            /* Ressource */
            'ressource'         => 'min:5',
            /* Object */
            'object'            => 'min:5'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            /* Text */
            'must'     => 'Lore',
            'not_must' => 'Lorem ipsum',
            'size'     => new \stdClass()
        ])->setRules([
            /* Text */
            'must'     => 'min:5',
            'not_must' => '!min:5',
            'size'     => '!min:5'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
        fclose($stream);
    }

    public function testMinNumeric(): void
    {
        $this->object->setInputs([
            /* Integer */
            'int'                    => 5,
            'not_int'                => 4,
            'int_required'           => 5,
            'int_not_required'       => '',
            /* Integer Numeric */
            'numeric_int'            => '5',
            'not_numeric_int'        => '4',
            'numeric_int_required'   => '5',
            /* Float numeric */
            'numeric_float'          => '5.5',
            'not_numeric_float'      => '4.5',
            'numeric_float_required' => '5.5'
        ])->setRules([
            /* Integer */
            'int'                    => 'min_numeric:5',
            'not_int'                => '!min_numeric:5',
            'int_required'           => 'required|min_numeric:5',
            'int_not_required'       => '!required|min_numeric:5',
            /* Integer numeric */
            'numeric_int'            => 'min_numeric:5',
            'not_numeric_int'        => '!min_numeric:5',
            'numeric_int_required'   => 'required|min_numeric:5',
            /* Float numeric */
            'numeric_float'          => 'min_numeric:5.5',
            'not_numeric_float'      => '!min_numeric:5.0',
            'numeric_float_required' => 'required|min_numeric:5.5'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            /* Text */
            'must'     => 'Lore',
            'not_must' => 'Lorem ipsum',
            'size'     => new \stdClass()
        ])->setRules([
            /* Text */
            'must'     => 'min_numeric:5',
            'not_must' => '!min_numeric:5',
            'size'     => 'min_numeric:5'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    public function testMinExceptionMin(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('field', 4)
            ->addRule('field', 'min:error')
            ->isValid();
    }

    public function testExceptionArgMin(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The comparison argument must be a string or numeric.');
        $this->object
            ->addInput('args', true)
            ->addInput('field', '1')
            ->addRule('field', 'min:@args')
            ->isValid();
    }

    public function testExceptionArgMinNumeric(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The comparison argument must be a string or numeric.');
        $this->object
            ->addInput('args', true)
            ->addInput('field', '1')
            ->addRule('field', 'min_numeric:@args')
            ->isValid();
    }
}

class ObjectTest
{
    public function __toString(): string
    {
        return 'test content';
    }
}
