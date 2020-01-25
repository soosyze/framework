<?php

namespace Soosyze\Tests\Components\Validator;

use Soosyze\Components\Validator\Rule;
use Soosyze\Components\Validator\Validator;

class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Validator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Validator;
    }

    /**
     * @expectedException \Exception
     */
    public function testxception()
    {
        $this->object
            ->addInput('field', 4)
            ->addRule('field', 'exception')
            ->isValid();
    }

    public function testVoid()
    {
        $object = new Validator();
        $this->assertTrue($object->isValid());
    }

    public function testCustomTest()
    {
        Validator::addTest('cube', '\Soosyze\Tests\Components\Validator\Cube');
        Validator::addTest('double', '\Soosyze\Tests\Components\Validator\DoubleR');
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

    public function testCustomTestReturn()
    {
        Validator::addTest('cube', new Cube());
        $this->object->setInputs([
            'custom_cube'     => 5,
            'custom_not_cube' => 4
        ])->setRules([
            'custom_cube'     => 'cube:16',
            'custom_not_cube' => '!cube:16'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertEquals($this->object->getError('custom_cube.cube'), 'La valeur au cube de custom_cube n\'est pas égale à 4.');

        $this->object->setInputs([
            'custom_cube2'     => 4,
            'custom_not_cube2' => 2
        ])->setRules([
            'custom_cube2'     => 'cube:16',
            'custom_not_cube2' => '!cube:16'
        ]);

        $this->assertTrue($this->object->isValid());
    }

    public function testCustomLabel()
    {
        $this->object
            ->setLabel([ 'field' => 'Text input' ])
            ->addInput('field', 10)
            ->addRule('field', 'string');

        $this->assertFalse($this->object->isValid());
        $this->assertEquals($this->object->getError('field.string'), 'La valeur de Text input n\'est pas une chaine de caractères.');
    }

    public function testCustomMessage()
    {
        Validator::setMessages([
            'string' => [
                'not' => 'My message custom for :label !'
            ]
        ]);

        $this->object->setInputs([
            'field' => 'hello world !'
        ])->setRules([
            'field' => '!string'
        ]);

        $this->object->isValid();

        $this->assertEquals($this->object->getError('field.string'), 'My message custom for field !');
    }

    public function testNoInput()
    {
        $this->object->setInputs([
            'field'  => 'Lorem ipsum',
            'field2' => ''
        ])->setRules([
            'field' => 'string'
        ]);

        $this->assertTrue($this->object->isValid());
    }

    public function testNoRule()
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

class Cube extends Rule
{
    protected function test($key, $value, $arg, $not = true)
    {
        if ($value * $value != $arg && $not) {
            $this->addReturn($key, 'must');
        } elseif ($value * $value == $arg && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    protected function messages()
    {
        return [
            'must' => 'La valeur au cube de :label n\'est pas égale à 4.',
            'not'  => 'La valeur au cube de :label ne doit pas être égale à 4.'
        ];
    }
}

class DoubleR extends Rule
{
    protected function test($key, $value, $arg, $not = true)
    {
        if ($value * 2 != 16 && $not) {
            $this->addReturn($key, 'must');
        } elseif ($value * 2 == 16 && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    protected function messages()
    {
        return [
            'must' => 'Le double de la valeur de :label n\'est pas égale à 16.',
            'not'  => 'Le double de la valeur de :label ne doit pas être égale à 16.'
        ];
    }
}
