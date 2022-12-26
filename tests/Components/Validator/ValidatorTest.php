<?php

namespace Soosyze\Tests\Components\Validator;

use Soosyze\Components\Validator\Validator;
use Soosyze\Tests\Resources\Validator\Cube;
use Soosyze\Tests\Resources\Validator\DoubleR;

class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Validator
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Validator;
    }

    public function testException(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('field', 4)
            ->addRule('field', 'exception')
            ->isValid();
    }

    public function testVoid(): void
    {
        $object = new Validator();
        $this->assertTrue($object->isValid());
    }

    public function getInput(): void
    {
        $this->object->setInputs([
                'field_array'   => [ 'foo' ],
                'field_bool'    => 'on',
                'field_int'     => 1,
                'field_mixed'   => new \stdClass(),
                'field_numeric' => '1',
                'field_string'  => 'foo',
            ])
            ->setRules([
                'field_array'   => '!required',
                'field_bool'    => '!required',
                'field_int'     => '!required',
                'field_mixed'   => '!required',
                'field_numeric' => '!required',
                'field_string'  => '!required'
        ]);

        $this->assertEquals([ 'foo' ], $this->object->getInputArray('field_array'));
        $this->assertEquals(true, $this->object->getInputBool('field_bool'));
        $this->assertEquals(1, $this->object->getInputInt('field_int'));
        $this->assertEquals(new \stdClass(), $this->object->getInput('field_mixed'));
        $this->assertEquals(1, $this->object->getInputInt('field_numeric'));
        $this->assertEquals('foo', $this->object->getInputString('field_string'));

        $this->assertEquals([], $this->object->getInputArray('field_empty_array'));
        $this->assertEquals(false, $this->object->getInputBool('field_empty_bool'));
        $this->assertEquals(0, $this->object->getInputInt('field_empty_int'));
        $this->assertEquals(null, $this->object->getInput('field_empty_mixed'));
        $this->assertEquals(0, $this->object->getInputInt('field_empty_numeric'));
        $this->assertEquals('', $this->object->getInputString('field_empty_string'));
    }

    public function testGetInputs(): void
    {
        $this->object->setInputs([
                'field_1' => 1,
                'field_2' => 2,
                'field_3' => 3,
                'field_4' => 4,
                'field_5' => 5
            ])
            ->setRules([
                'field_1' => '!required',
                'field_2' => '!required',
                'field_3' => '!required'
        ]);

        $this->assertEquals(
            [
                'field_1' => 1,
                'field_2' => 2,
                'field_3' => 3
            ],
            $this->object->getInputs()
        );
    }

    public function testGetInputsWithout(): void
    {
        $this->object->setInputs([
            'field_1' => 1,
            'field_2' => 2,
            'field_3' => 3,
            'field_4' => 4,
            'field_5' => 5
        ]);

        $this->assertEquals(
            [],
            $this->object->getInputsWithout()
        );

        $this->object->setRules([
            'field_1' => '!required',
            'field_2' => '!required',
            'field_3' => '!required',
            'field_4' => '!required',
            'field_5' => '!required'
        ]);

        $this->assertEquals(
            [
                'field_1' => 1,
                'field_2' => 2,
                'field_3' => 3,
                'field_4' => 4,
                'field_5' => 5
            ],
            $this->object->getInputsWithout()
        );
        $this->assertEquals(
            [
                'field_3' => 3,
                'field_4' => 4,
                'field_5' => 5
            ],
            $this->object->getInputsWithout([ 'field_1', 'field_2' ])
        );
    }

    public function testGetInputsWithoutObject(): void
    {
        $this->object->setInputs([
            'field_1' => 1,
            'field_2' => 2,
            'field_3' => 3,
            'field_4' => 4,
            'field_5' => new \stdClass()
        ]);

        $this->assertEquals([], $this->object->getInputsWithoutObject());

        $this->object->setRules([
            'field_1' => '!required',
            'field_2' => '!required',
            'field_3' => '!required',
            'field_4' => '!required',
            'field_5' => '!required'
        ]);

        $this->assertEquals(
            [
                'field_1' => 1,
                'field_2' => 2,
                'field_3' => 3,
                'field_4' => 4
            ],
            $this->object->getInputsWithoutObject()
        );
        $this->assertEquals(
            [
                'field_3' => 3,
                'field_4' => 4
            ],
            $this->object->getInputsWithoutObject([ 'field_1', 'field_2' ])
        );
    }

    public function testCustomTest(): void
    {
        Validator::addTestGlobal('cube', Cube::class);
        Validator::addTestGlobal('double', DoubleR::class);
        $this->object->setInputs([
            'custom_cube'      => 4,
            'custom_not_cube'  => 2,
            'custom_multi'     => 8,
            'custom_not_multi' => 2
        ])->setRules([
            'custom_cube'      => 'cube:16',
            'custom_not_cube'  => '!cube:16',
            'custom_multi'     => 'double',
            'custom_not_multi' => '!double'
        ]);

        $this->assertTrue($this->object->isValid());
    }

    public function testCustomTestReturn(): void
    {
        Validator::addTestGlobal('cube', Cube::class);
        $this->object->setInputs([
            'custom_cube'     => 5,
            'custom_not_cube' => 4
        ])->setRules([
            'custom_cube'     => 'cube:16',
            'custom_not_cube' => '!cube:16'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertEquals(
            [ 'cube' => 'La valeur au cube de custom_cube n\'est pas égale à 4.' ],
            $this->object->getError('custom_cube')
        );

        $this->object->setInputs([
            'custom_cube2'     => 4,
            'custom_not_cube2' => 2
        ])->setRules([
            'custom_cube2'     => 'cube:16',
            'custom_not_cube2' => '!cube:16'
        ]);

        $this->assertTrue($this->object->isValid());
    }

    public function testCustomLabel(): void
    {
        $this->object
            ->setLabels([ 'field' => 'Text input' ])
            ->addInput('field', 10)
            ->addRule('field', 'string');

        $this->assertFalse($this->object->isValid());
        $this->assertEquals(
            [ 'string' => 'The value of the Text input field must be a character string.' ],
            $this->object->getError('field')
        );
    }

    public function testCustomMessage(): void
    {
        $this->object
            ->setInputs([
                'field' => 'hello world !'
            ])
            ->setRules([
                'field' => '!string'
            ])
            ->setMessages([
                'field' => [
                    'string' => [
                        'not' => 'My message custom for :label !'
                    ]
                ]
            ])
            ->isValid();

        $this->assertEquals(
            [ 'string' => 'My message custom for field !' ],
            $this->object->getError('field')
        );
    }

    public function testCustomMessageGlobal(): void
    {
        Validator::setMessagesGlobal([
            'string' => [
                'not' => 'My message custom global for :label !'
            ]
        ]);

        $this->object
            ->setInputs([
                'field' => 'hello world !'
            ])
            ->setRules([
                'field' => '!string'
            ])
            ->isValid();

        $this->assertEquals(
            [ 'string' => 'My message custom global for field !' ],
            $this->object->getError('field')
        );
    }

    public function testCustomAttributs(): void
    {
        $this->object
            ->setInputs([
                'field'   => 'MyValue',
                'field_2' => 'OtherValue'
            ])
            ->setRules([
                'field' => 'equal:@field_2'
            ])
            ->setAttributs([
                'field' => [
                    'equal'  => [
                        ':label' => function ($label): string {
                            return strtoupper($label);
                        },
                        ':value' => function ($value): string {
                            return 'field_2 : ' . $value;
                        }
                    ]
                ]
            ])
            ->isValid();

        $this->assertEquals(
            [ 'equal' => 'The FIELD field must be equal to field_2 : OtherValue.' ],
            $this->object->getError('field')
        );
    }

    public function testNoInput(): void
    {
        $this->object->setInputs([
            'field'  => 'Lorem ipsum',
            'field2' => ''
        ])->setRules([
            'field' => 'string'
        ]);

        $this->assertTrue($this->object->isValid());
    }

    public function testNoRule(): void
    {
        $this->object->setInputs([
            'field' => 'Lorem ipsum',
        ])->setRules([
            'field'  => 'string',
            'field2' => 'required|string'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }
}
