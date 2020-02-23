<?php

namespace Soosyze\Tests\Components\Validator;

use Soosyze\Components\Validator\Validator;
use Soosyze\Components\Validator\ValidatorIterator;

class ValidatorIteratorTest extends \PHPUnit\Framework\TestCase
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

    public function testIteratorSimple()
    {
        $this->object->setInputs([
            'field_1' => 'test',
            'field_2' => [
                [
                    'name'      => 'foo',
                    'firstname' => 'bar'
                ], [
                    'name'      => 'foo',
                    'firstname' => 'bar'
                ]
            ]
        ])->setRules([
            'field_1' => 'required|string',
            'field_2' => (new ValidatorIterator)
                ->setRules([
                    'name'      => 'required|string',
                    'firstname' => '!required|string'
                ])
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_1' => '',
            'field_2' => [
                [
                    'name'      => 'foo',
                    'firstname' => ''
                ], [
                    'name'      => '',
                    'firstname' => 'bar'
                ]
            ]
        ])->setRules([
            'field_1' => 'required|string',
            'field_2' => (new ValidatorIterator)
                ->setRules([
                    'name'      => 'required|string',
                    'firstname' => '!required|string'
                ])
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
        $this->assertCount(2, $this->object->getKeyErrors());
    }

    public function testMultiple()
    {
        $this->object
            ->setInputs([
                'field_simple' => 'test',
                'field_object' => [
                    'sub_field_1' => 'value_1',
                    'sub_field_2' => 'value_2'
                ]
            ])
            ->setRules([
                'field_simple' => 'required|string',
                'field_object' => (new Validator)
                ->setRules([
                    'sub_field_1' => 'required|string',
                    'sub_field_2' => 'required|string'
                ])
            ]);

        $this->assertTrue($this->object->isValid());

        $this->object
            ->setInputs([
                'field_simple' => '',
                'field_object' => [
                    'sub_field_1' => '',
                    'sub_field_2' => ''
                ]
            ])
            ->setRules([
                'field_simple' => 'required|string',
                'field_object' => (new Validator)
                ->setRules([
                    'sub_field_1' => 'required|string',
                    'sub_field_2' => 'required|string'
                ])
            ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
        $this->assertCount(3, $this->object->getKeyErrors());
    }
}
