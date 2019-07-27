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
            ->addInput('field_between', 4)
            ->addRule('field_between', 'exception')
            ->isValid();
    }
    
    public function testAccepted()
    {
        $this->object->setInputs([
            /* true */
            'field_accepted_true'            => true,
            'field_accepted_true_text'       => 'true',
            'field_accepted_true_one'        => 1,
            'field_accepted_true_one_text'   => '1',
            'field_accepted_true_on'         => 'on',
            'field_accepted_true_yes'        => 'yes'
        ])->setRules([
            /* true */
            'field_accepted_true'            => 'accepted',
            'field_accepted_true_text'       => 'accepted',
            'field_accepted_true_one'        => 'accepted',
            'field_accepted_true_one_text'   => 'accepted',
            'field_accepted_true_on'         => 'accepted',
            'field_accepted_true_yes'        => 'accepted'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_accepted'     => 'not bool',
            'field_not_accepted' => true
        ])->setRules([
            'field_accepted'     => 'accepted',
            'field_not_accepted' => '!accepted'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testAlphaNum()
    {
        $this->object->setInputs([
            'field_alpha_num'              => 'hello2000',
            'field_not_alpha_num'          => 'hello&2000@',
            'field_alpha_num_required'     => 'hello2000',
            'field_alpha_num_not_required' => ''
        ])->setRules([
            'field_alpha_num'              => 'AlphaNum',
            'field_not_alpha_num'          => '!AlphaNum',
            'field_alpha_num_required'     => 'required|AlphaNum',
            'field_alpha_num_not_required' => '!required|AlphaNum',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_alpha_num'     => [],
            'field_not_alpha_num' => 'hello2000'
        ])->setRules([
            'field_alpha_num'     => 'AlphaNum',
            'field_not_alpha_num' => '!AlphaNum'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testAlphaNumText()
    {
        $this->object->setInputs([
            'field_alpha_num'              => 'hello2000.',
            'field_not_alpha_num'          => 'hello&2000@',
            'field_alpha_num_required'     => 'hello2000!',
            'field_alpha_num_not_required' => ''
        ])->setRules([
            'field_alpha_num'              => 'AlphaNumText',
            'field_not_alpha_num'          => '!AlphaNumText',
            'field_alpha_num_required'     => 'required|AlphaNumText',
            'field_alpha_num_not_required' => '!required|AlphaNumText',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_alpha_num'     => 'hello&2000@',
            'field_not_alpha_num' => 'hello2000.',
        ])->setRules([
            'field_alpha_num'     => 'AlphaNumText',
            'field_not_alpha_num' => '!AlphaNumText',
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testArray()
    {
        $this->object->setInputs([
            'field_array'              => [ 0, 1, 2 ],
            'field_not_array'          => 'not array',
            'field_array_required'     => [ 0, 1, 2 ],
            'field_array_not_required' => ''
        ])->setRules([
            'field_array'              => 'array',
            'field_not_array'          => '!array',
            'field_array_required'     => 'required|array',
            'field_array_not_required' => '!required|array',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_array'     => 'not array',
            'field_not_array' => [ 0, 1, 2 ]
        ])->setRules([
            'field_array'     => 'array',
            'field_not_array' => '!array'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testBetween()
    {
        $this->object->setInputs([
            /* Text */
            'field_text_between_min'           => 'Lorem',
            'field_text_between_max'           => 'Lorem ipsum doe',
            'field_not_text_between'           => 'Lore',
            'field_text_between_required'      => 'Lorem ipsum',
            'field_text_between_not_required'  => '',
            /* Numeric */
            'field_int_between_min'            => 5,
            'field_int_between_max'            => 15,
            'field_not_int_between'            => 16,
            'field_int_between_min_required'   => 5,
            'field_int_between_max_required'   => 15,
            'field_int_between_not_required'   => '',
            /* Array */
            'field_array_between_min'          => [ 1, 2, 3, 4, 5 ],
            'field_array_between_max'          => [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ],
            'field_not_array_between'          => [ 1, 2, 3, 4 ],
            'field_array_between_min_required' => [ 1, 2, 3, 4, 5 ],
            'field_array_between_max_required' => [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ],
            'field_array_between_not_required' => '',
        ])->setRules([
            /* Text */
            'field_text_between_min'           => 'between:5,15',
            'field_text_between_max'           => 'between:5,15',
            'field_not_text_between'           => '!between:5,15',
            'field_text_between_required'      => 'required|between:5,15',
            'field_text_between_not_required'  => '!required|between:5,15',
            /* Numeric */
            'field_int_between_min'            => 'between:5,15',
            'field_int_between_max'            => 'between:5,15',
            'field_not_int_between'            => '!between:5,15',
            'field_int_between_min_required'   => 'required|between:5,15',
            'field_int_between_max_required'   => 'required|between:5,15',
            'field_int_between_not_required'   => '!required|between:5,15',
            /* Numeric */
            'field_array_between_min'          => 'between:5,10',
            'field_array_between_max'          => 'between:5,10',
            'field_not_array_between'          => '!between:5,10',
            'field_array_between_min_required' => 'required|between:5,15',
            'field_array_between_max_required' => 'required|between:5,15',
            'field_array_between_not_required' => '!required|between:5,15',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            /* Text */
            'field_text_between_min' => 'Lor',
            'field_text_between_max' => 'Lorem ip'
        ])->setRules([
            /* Text */
            'field_text_between_min' => 'between:5,10',
            'field_text_between_max' => '!between:5,10'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
        $this->assertArraySubset($this->object->getKeyInputErrors(), [
            'field_text_between_min', 'field_text_between_max'
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testBetweenMissingException()
    {
        $this->object
            ->addInput('field_between', 4)
            ->addRule('field_between', 'between')
            ->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testBetweenExceptionTypeMin()
    {
        $this->object
            ->addInput('field_between', 4)
            ->addRule('field_between', 'between:error,5')
            ->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testBetweenExceptionTypeMax()
    {
        $this->object
            ->addInput('field_between', 4)
            ->addRule('field_between', 'between:1,error')
            ->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testBetweenMinUpperMax()
    {
        $this->object
            ->addInput('field_between', 4)
            ->addRule('field_between', 'between:10,1')
            ->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testBetweenExceptionValue()
    {
        $this->object
            ->addInput('field_between', new \stdClass())
            ->addRule('field_between', 'between:1,10')
            ->isValid();
    }

    public function testBool()
    {
        $this->object->setInputs([
            /* true */
            'field_bool_true'            => true,
            'field_bool_true_text'       => 'true',
            'field_bool_true_one'        => 1,
            'field_bool_true_one_text'   => '1',
            'field_bool_true_on'         => 'on',
            'field_bool_true_yes'        => 'yes',
            /* false */
            'field_bool_false'           => false,
            'field_bool_false_text'      => 'false',
            'field_bool_false_zero'      => 0,
            'field_bool_false_zero_text' => '0',
            'field_bool_false_off'       => 'off',
            'field_bool_false_no'        => 'no',
            'field_bool_false_void'      => ''
        ])->setRules([
            /* true */
            'field_bool_true'            => 'bool',
            'field_bool_true_text'       => 'bool',
            'field_bool_true_one'        => 'bool',
            'field_bool_true_one_text'   => 'bool',
            'field_bool_true_on'         => 'bool',
            'field_bool_true_yes'        => 'bool',
            /* false */
            'field_bool_false'           => 'bool',
            'field_bool_false_text'      => 'bool',
            'field_bool_false_zero'      => 'bool',
            'field_bool_false_zero_text' => 'bool',
            'field_bool_false_off'       => 'bool',
            'field_bool_false_no'        => 'bool',
            'field_bool_false_void'      => 'bool'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_bool'     => 'not bool',
            'field_not_bool' => true
        ])->setRules([
            'field_bool'     => 'bool',
            'field_not_bool' => '!bool'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
    
    public function testColorHex()
    {
        $this->object->setInputs([
            'field_color3_default'     => '#FFF',
            'field_color6_default'     => '#FFFFFF',
            'field_color3'             => '#FFF',
            'field_not_color3'         => 'not color',
            'field_color6'             => '#FFFFFF',
            'field_not_color6'         => 'not color',
            'field_color_required'     => '#FFF',
            'field_color_not_required' => ''
        ])->setRules([
            'field_color3_default'     => 'colorhex',
            'field_color6_default'     => 'colorhex',
            'field_color3'             => 'colorhex:3',
            'field_not_color3'         => '!colorhex:3',
            'field_color6'             => 'colorhex:6',
            'field_not_color6'         => '!colorhex:6',
            'field_color_required'     => 'required|colorhex',
            'field_color_not_required' => '!required|colorhex',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_color6_symbol' => 'FFFFFF',
            'field_color6_letter' => '#FFFFFM',
            'field_color6_length' => '#FFFFFFF',
            'field_color3_symbol' => 'FFF',
            'field_color3_letter' => '#FFM',
            'field_color3_length' => '#FFFF',
        ])->setRules([
            'field_color6_symbol' => 'colorhex:6',
            'field_color6_letter' => 'colorhex:6',
            'field_color6_length' => 'colorhex:6',
            'field_color3_symbol' => 'colorhex:3',
            'field_color3_letter' => 'colorhex:3',
            'field_color3_length' => 'colorhex:3',
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(6, $this->object->getErrors());
    }
    
    /**
     * @expectedException \Exception
     */
    public function testColorHexException()
    {
        $this->object->addInput('field_color3_default', '#FFF')
            ->addRule('field_color3_default', 'colorhex:4')
            ->isValid();
    }

    public function testDate()
    {
        $this->object->setInputs([
            'field_date'              => '10/01/1994',
            'field_not_date'          => 'not date',
            'field_date_required'     => '10/01/1994',
            'field_date_not_required' => ''
        ])->setRules([
            'field_date'              => 'date',
            'field_not_date'          => '!date',
            'field_date_required'     => 'required|date',
            'field_date_not_required' => '!required|date',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_date'     => 'not date',
            'field_not_date' => '10/01/1994'
        ])->setRules([
            'field_date'     => 'date',
            'field_not_date' => '!date'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testDateFormat()
    {
        $this->object->setInputs([
            'field_date'              => '10/01/1994',
            'field_not_date'          => '1994/01/10',
            'field_date_required'     => '10/01/1994',
            'field_date_not_required' => ''
        ])->setRules([
            'field_date'              => 'date_format:j/n/Y',
            'field_not_date'          => '!date_format:j/n/Y',
            'field_date_required'     => 'required|date_format:j/n/Y',
            'field_date_not_required' => '!required|date_format:j/n/Y',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_date_error' => 'not date',
            'field_date'       => '1994/10/01',
            'field_not_date'   => '10/01/1994'
        ])->setRules([
            'field_date_error' => 'date_format:j/n/Y',
            'field_date'       => 'date_format:j/n/Y',
            'field_not_date'   => '!date_format:j/n/Y'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    public function testDateAfter()
    {
        $this->object->setInputs([
            'field_date'              => '10/02/1994',
            'field_not_date'          => '09/01/1994',
            'field_date_required'     => '10/02/1994',
            'field_date_not_required' => ''
        ])->setRules([
            'field_date'              => 'date_after:10/01/1994',
            'field_not_date'          => '!date_after:10/01/1994',
            'field_date_required'     => 'required|date_after:10/01/1994',
            'field_date_not_required' => '!required|date_after:10/01/1994',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_date_error'  => 'not date',
            'field_date_error2' => '10/01/1994',
            'field_date'        => '10/01/1994',
            'field_not_date'    => '10/02/1994',
        ])->setRules([
            'field_date_error'  => 'date_after:10/01/1994',
            'field_date_error2' => 'date_after:error',
            'field_date'        => 'date_after:10/01/1994',
            'field_not_date'    => '!date_after:10/01/1994',
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(4, $this->object->getErrors());
    }

    public function testDateAfterOrEqual()
    {
        $this->object->setInputs([
            'field_date'              => '10/01/1994',
            'field_not_date'          => '09/01/1994',
            'field_date_required'     => '10/02/1994',
            'field_date_not_required' => ''
        ])->setRules([
            'field_date'              => 'date_after_or_equal:10/01/1994',
            'field_not_date'          => '!date_after_or_equal:10/01/1994',
            'field_date_required'     => 'required|date_after_or_equal:10/01/1994',
            'field_date_not_required' => '!required|date_after_or_equal:10/01/1994'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_date'     => '09/01/1994',
            'field_not_date' => '10/01/1994'
        ])->setRules([
            'field_date'     => 'date_after_or_equal:10/01/1994',
            'field_not_date' => '!date_after_or_equal:10/01/1994'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testDateBefore()
    {
        $this->object->setInputs([
            'field_date'              => '09/01/1994',
            'field_not_date'          => '10/01/1994',
            'field_date_required'     => '09/01/1994',
            'field_date_not_required' => ''
        ])->setRules([
            'field_date'              => 'date_before:10/01/1994',
            'field_not_date'          => '!date_before:10/01/1994',
            'field_date_required'     => 'required|date_before:10/01/1994',
            'field_date_not_required' => '!required|date_before:10/01/1994',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_date_error'  => 'not date',
            'field_date_error2' => '10/01/1994',
            'field_date'        => '11/01/1994',
            'field_not_date'    => '09/01/1994',
        ])->setRules([
            'field_date_error'  => 'date_before:10/01/1994',
            'field_date_error2' => 'date_before:error',
            'field_date'        => 'date_before:10/01/1994',
            'field_not_date'    => '!date_before:10/01/1994'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(4, $this->object->getErrors());
    }

    public function testDateBeforeOrEqual()
    {
        $this->object->setInputs([
            'field_date'              => '10/01/1994',
            'field_not_date'          => '10/01/1994',
            'field_date_required'     => '10/01/1994',
            'field_date_not_required' => ''
        ])->setRules([
            'field_date'              => 'date_before_or_equal:10/01/1994',
            'field_not_date'          => '!date_before_or_equal:09/01/1994',
            'field_date_required'     => 'required|date_before_or_equal:10/01/1994',
            'field_date_not_required' => '!required|date_before_or_equal:09/01/1994',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_date'     => '10/01/1994',
            'field_not_date' => '10/01/1994'
        ])->setRules([
            'field_date'     => 'date_before_or_equal:09/01/1994',
            'field_not_date' => '!date_before_or_equal:10/01/1994'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testDir()
    {
        $this->object->setInputs([
            'field_dir'              => __DIR__,
            'field_not_dir'          => 'not dir',
            'field_dir_required'     => __DIR__,
            'field_dir_not_required' => ''
        ])->setRules([
            'field_dir'              => 'dir',
            'field_not_dir'          => '!dir',
            'field_dir_required'     => 'required|dir',
            'field_dir_not_required' => '!required|dir',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_dir'     => 'not dir',
            'field_not_dir' => __DIR__
        ])->setRules([
            'field_dir'     => 'dir',
            'field_not_dir' => '!dir',
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testEqual()
    {
        $this->object->setInputs([
            'field_equals'              => 'hello',
            'field_not_equals'          => 'not hello',
            'field_equals_required'     => 'hello',
            'field_equals_not_required' => '',
            'field_equals_ref'          => 'hello'
        ])->setRules([
            'field_equals'              => 'equal:hello',
            'field_not_equals'          => '!equal:hello',
            'field_equals_required'     => 'required|equal:hello',
            'field_equals_not_required' => '!required|equal:hello',
            'field_equals_ref'          => 'equal:@field_equals'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_equals'     => 'not hello',
            'field_not_equals' => 'hello',
        ])->setRules([
            'field_equals'     => 'equal:hello',
            'field_not_equals' => '!equal:hello',
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testFloat()
    {
        $this->object->setInputs([
            'field_float'              => 10.1,
            'field_not_foat_text'      => 'not float',
            'field_not_float_int'      => 10,
            'field_not_float_array'    => [ 1, 2 ],
            'field_float_required'     => 10.1,
            'field_float_not_required' => ''
        ])->setRules([
            'field_float'              => 'float',
            'field_not_foat_text'      => '!float',
            'field_not_float_int'      => '!float',
            'field_not_float_array'    => '!float',
            'field_float_required'     => 'required|float',
            'field_float_not_required' => '!required|float'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_float'    => 'not float',
            'field_not_foat' => 10.1
        ])->setRules([
            'field_float'    => 'float',
            'field_not_foat' => '!float'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testEmail()
    {
        $this->object->setInputs([
            'field_email'              => 'my-mail@mail.fr',
            'field_not_email'          => 'not mail',
            'field_email_required'     => 'my-mail@mail.fr',
            'field_email_not_required' => ''
        ])->setRules([
            'field_email'              => 'email',
            'field_not_email'          => '!email',
            'field_email_required'     => 'required|email',
            'field_email_not_required' => '!required|email'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_email'     => 'not mail',
            'field_not_email' => 'my-mail@mail.fr'
        ])->setRules([
            'field_email'     => 'email',
            'field_not_email' => '!email'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testIp()
    {
        $this->object->setInputs([
            'field_ip'              => '127.0.0.1',
            'field_not_ip'          => 'no ip',
            'field_ip_required'     => '127.0.0.1',
            'field_ip_not_required' => ''
        ])->setRules([
            'field_ip'              => 'ip',
            'field_not_ip'          => '!ip',
            'field_ip_required'     => 'required|ip',
            'field_ip_not_required' => '!required|ip'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_ip'     => 'no ip',
            'field_not_ip' => '127.0.0.1'
        ])->setRules([
            'field_ip'     => 'ip',
            'field_not_ip' => '!ip'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testInt()
    {
        $this->object->setInputs([
            'field_int'              => 10,
            'field_int_text'         => '10',
            'field_not_int_text'     => 'not int',
            'field_not_int_float'    => 10.1,
            'field_not_int_array'    => [ 1, 2 ],
            'field_int_required'     => 10,
            'field_int_not_required' => ''
        ])->setRules([
            'field_int'              => 'int',
            'field_int_text'         => 'int',
            'field_not_int_text'     => '!int',
            'field_not_int_float'    => '!int',
            'field_not_int_array'    => '!int',
            'field_int_required'     => 'required|int',
            'field_int_not_required' => '!required|int'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_int'          => 'not int',
            'field_not_int_text' => 10
        ])->setRules([
            'field_int'          => 'int',
            'field_not_int_text' => '!int'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testJson()
    {
        $json = '{"key":"value"}';

        $this->object->setInputs([
            'field_json'              => $json,
            'field_not_json'          => 'no json',
            'field_json_required'     => $json,
            'field_json_not_required' => ''
        ])->setRules([
            'field_json'              => 'json',
            'field_not_json'          => '!json',
            'field_json_required'     => 'required|json',
            'field_json_not_required' => '!required|json'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_json'     => 'no json',
            'field_not_json' => $json
        ])->setRules([
            'field_json'     => 'json',
            'field_not_json' => '!json'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testInArray()
    {
        $this->object->setInputs([
            'field_inArray'              => '1',
            'field_not_inArray'          => '1',
            'field_inArray_required'     => '1',
            'field_inArray_not_required' => ''
        ])->setRules([
            'field_inArray'              => 'inArray:1,2,3,4,5',
            'field_not_inArray'          => '!inArray:2,3,4,5',
            'field_inArray_required'     => 'required|inArray:1,2,3,4,5',
            'field_inArray_not_required' => '!required|inArray:1,2,3,4,5'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_inArray'     => '1',
            'field_not_inArray' => '1'
        ])->setRules([
            'field_inArray'     => 'inArray:2,3,4,5',
            'field_not_inArray' => '!inArray:1,2,3,4,5'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testMax()
    {
        $this->object->setInputs([
            /* Text */
            'field_text_max'              => 'Lorem',
            'field_not_text_max'          => 'Lorem ipsum',
            'field_text_max_required'     => 'Lore',
            'field_text_max_not_required' => '',
            /* Entier */
            'field_int_max'               => 5,
            'field_not_int_max'           => 6,
            'field_int_max_required'      => 5,
            'field_int_max_not_required'  => '',
            /* Numeric */
            'field_numeric_max'           => '5',
            'field_not_numeric_max'       => '6',
            'field_numeric_max_required'  => '5',
            /* Tableau */
            'field_array_max'             => [ 1, 2, 3, 4, 5 ],
            'field_not_array_max'         => [ 1, 2, 3, 4, 5, 6 ]
        ])->setRules([
            /* Text */
            'field_text_max'              => 'max:5',
            'field_not_text_max'          => '!max:5',
            'field_text_max_required'     => 'required|max:5',
            'field_text_max_not_required' => '!required|max:5',
            /* Entier */
            'field_int_max'               => 'max:5',
            'field_not_int_max'           => '!max:5',
            'field_int_max_required'      => 'required|max:5',
            'field_int_max_not_required'  => '!required|max:5',
            /* Numeric */
            'field_numeric_max'           => 'max:5',
            'field_not_numeric_max'       => '!max:5',
            'field_numeric_max_required'  => 'required|max:5',
            /* Tableau */
            'field_array_max'             => 'max:5',
            'field_not_array_max'         => '!max:5'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            /* Text */
            'field_text_max'     => 'Lorem ipsum',
            'field_not_text_max' => 'Lorem'
        ])->setRules([
            /* Text */
            'field_text_max'     => 'max:5',
            'field_not_text_max' => '!max:5'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    /**
     * @expectedException \Exception
     */
    public function testMaxExceptionMin()
    {
        $this->object
            ->addInput('field_text_max', 4)
            ->addRule('field_text_max', 'max:error')
            ->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testMaxExceptionValue()
    {
        $this->object
            ->addInput('field_text_max', new \stdClass())
            ->addRule('field_text_max', 'max:5')
            ->isValid();
    }

    public function testMin()
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, 'test content');

        $this->object->setInputs([
            /* Text */
            'field_text_min'                   => 'Lorem ipsum',
            'field_not_text_min'               => 'Lore',
            'field_text_min_required'          => 'Lorem ipsum',
            'field_text_min_not_required'      => '',
            /* Entier */
            'field_int_min'                    => 5,
            'field_not_int_min'                => 4,
            'field_int_min_required'           => 5,
            'field_int_min_not_required'       => '',
            /* Numeric entier */
            'field_numeric_int_min'            => '5',
            'field_not_numeric_int_min'        => '4',
            'field_numeric_int_min_required'   => '5',
            /* Numeric flottant */
            'field_numeric_float_min'          => '5.5',
            'field_not_numeric_float_min'      => '4.5',
            'field_numeric_int_float_required' => '5.5',
            /* Tableau */
            'field_array_min'                  => [ 1, 2, 3, 4, 5 ],
            'field_not_array_min'              => [ 1, 2, 3, 4 ],
            /* Ressource */
            'field_ressource_min'              => $stream,
            /* Object */
            'field_object_min'                 => new ObjectTest
        ])->setRules([
            /* Text */
            'field_text_min'                   => 'min:5',
            'field_not_text_min'               => '!min:5',
            'field_text_min_required'          => 'required|min:5',
            'field_text_min_not_required'      => '!required|min:5',
            /* Entier */
            'field_int_min'                    => 'min:5',
            'field_not_int_min'                => '!min:5',
            'field_int_min_required'           => 'required|min:5',
            'field_int_min_not_required'       => '!required|min:5',
            /* Numeric entier */
            'field_numeric_int_min'            => 'min:5',
            'field_not_numeric_int_min'        => '!min:5',
            'field_numeric_int_min_required'   => 'required|min:5',
            /* Numeric flottant */
            'field_numeric_float_min'          => 'min:5.5',
            'field_not_numeric_float_min'      => '!min:5.0',
            'field_numeric_int_float_required' => 'required|min:5.5',
            /* Tableau */
            'field_array_min'                  => 'min:5',
            'field_not_array_min'              => '!min:5',
            /* Ressource */
            'field_ressource_min'              => 'min:5',
            /* Object */
            'field_object_min'                 => 'min:5'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            /* Text */
            'field_text_min'     => 'Lore',
            'field_not_text_min' => 'Lorem ipsum'
        ])->setRules([
            /* Text */
            'field_text_min'     => 'min:5',
            'field_not_text_min' => '!min:5'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
        fclose($stream);
    }

    /**
     * @expectedException \Exception
     */
    public function testMinExceptionMin()
    {
        $this->object
            ->addInput('field_text_min', 4)
            ->addRule('field_text_min', 'min:error')
            ->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testMinExceptionValue()
    {
        $this->object
            ->addInput('field_text_min', new \stdClass())
            ->addRule('field_text_min', 'min:5')
            ->isValid();
    }

    public function testRequiredWith()
    {
        $this->object->setInputs([
            'field_int'   => '',
            'field_int_2' => ''
        ])->setRules([
            'field_int'            => '!required|int',
            'field_int_2'          => '!required|int',
            'field_required_whith' => 'required_with:field_int,field_int_2'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_int'   => 1,
            'field_int_2' => ''
        ])->setRules([
            'field_int'            => '!required|int',
            'field_int_2'          => '!required|int',
            'field_required_whith' => 'required_with:field_int,field_int_2'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_int'   => 1,
            'field_int_2' => 1
        ])->setRules([
            'field_int'            => '!required|int',
            'field_int_2'          => '!required|int',
            'field_required_whith' => 'required_with:field_int,field_int_2'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }

    /**
     * @expectedException \Exception
     */
    public function testRequiredWithException()
    {
        $this->object->setRules([
            'field_required_whith' => 'required_with:field_error'
        ])->isValid();
    }

    /**
     * @expectedException \Exception
     */
    public function testRequiredWithVoidException()
    {
        $this->object->setRules([
            'field_required_whith' => 'required_with:'
        ])->isValid();
    }

    public function testRequiredWithout()
    {
        $this->object->setInputs([
            'field_int'   => 1,
            'field_int_2' => 1
        ])->setRules([
            'field_int'            => '!required|int',
            'field_int_2'          => '!required|int',
            'field_required_whith' => 'required_without:field_int,field_int_2'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_int'   => 1,
            'field_int_2' => ''
        ])->setRules([
            'field_int'            => '!required|int',
            'field_int_2'          => '!required|int',
            'field_required_whith' => 'required_without:field_int,field_int_2'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_int'   => '',
            'field_int_2' => ''
        ])->setRules([
            'field_int'            => '!required|int',
            'field_int_2'          => '!required|int',
            'field_required_whith' => 'required_without:field_int,field_int_2'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }
    
    /**
     * @expectedException \Exception
     */
    public function testRequiredWithoutException()
    {
        $this->object->setRules([
            'field_required_whith' => 'required_without:field_error'
        ])->isValid();
    }

    public function testRegex()
    {
        $this->object->setInputs([
            'field_regex'              => 'hello world',
            'field_not_regex'          => 'hello world',
            'field_regex_required'     => 'hello world',
            'field_regex_not_required' => ''
        ])->setRules([
            'field_regex'              => 'regex:/^h.*/',
            'field_not_regex'          => '!regex:/^w.*/',
            'field_regex_required'     => 'required|regex:/^h.*/',
            'field_regex_not_required' => '!required|regex:/^h.*/',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_regex'     => 'hello world',
            'field_not_regex' => 'hello world'
        ])->setRules([
            'field_regex'     => 'regex:/^w.*/',
            'field_not_regex' => '!regex:/^h.*/'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testSlug()
    {
        $this->object->setInputs([
            'field_slug'              => 'hello-world',
            'field_not_slug'          => 'hello world',
            'field_slug_required'     => 'hello_world',
            'field_slug_not_required' => ''
        ])->setRules([
            'field_slug'              => 'slug',
            'field_not_slug'          => '!slug',
            'field_slug_required'     => 'required|slug',
            'field_slug_not_required' => '!required|slug',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_slug'     => 'hello world',
            'field_not_slug' => 'hello-world'
        ])->setRules([
            'field_slug'     => 'slug',
            'field_not_slug' => '!slug'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testString()
    {
        $this->object->setInputs([
            'field_text'           => 'Lorem ipsum',
            'field_text_required'  => 'Lorem ipsum',
            'field_not_text_int'   => 10,
            'field_not_text_float' => 10.1,
            'field_not_text_array' => [ 1, 2 ]
        ])->setRules([
            'field_text'           => 'string',
            'field_text_required'  => 'required|string',
            'field_not_text_int'   => '!string',
            'field_not_text_float' => '!string',
            'field_not_text_array' => '!string'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_text_error'           => 10,
            'field_text_required_error'  => '',
            'field_not_text_int_error'   => 10,
            'field_not_text_float_error' => 10.1,
            'field_not_text_array_error' => [ 1, 2 ],
            'field_not_string'           => 'test'
        ])->setRules([
            'field_text_error'           => 'string',
            'field_text_required_error'  => 'required|string',
            'field_not_text_int_error'   => 'string',
            'field_not_text_float_error' => 'string',
            'field_not_text_array_error' => 'string',
            'field_not_string'           => '!string',
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(6, $this->object->getErrors());
    }

    public function testToken()
    {
        $_SESSION[ 'token' ]      = [
            'field_token'          => 'Lorem ipsum dolor sit amet',
            'field_token_required' => 'Lorem ipsum dolor sit amet'
        ];
        $_SESSION[ 'token_time' ]= [
            'field_token'          => time(),
            'field_token_required' => time()
        ];

        $this->object->setInputs([
            'field_token'              => 'Lorem ipsum dolor sit amet',
            'field_token_required'     => 'Lorem ipsum dolor sit amet',
            'field_token_not_required' => ''
        ])->setRules([
            'field_token'              => 'token',
            'field_token_required'     => 'required|token',
            'field_token_not_required' => '!required|token'
        ]);

        $this->assertTrue($this->object->isValid());
    }

    /**
     * @expectedException \Exception
     */
    public function testTokenException()
    {
        $_SESSION[ 'token' ]      = 'Lorem ipsum dolor sit amet';
        $_SESSION[ 'token_time' ] = time();

        $this->object
            ->addInput('field_token', 'error')
            ->addRule('field_token', 'token:error')
            ->isValid();
    }

    public function testTokenErrorSession()
    {
        @session_destroy();
        $this->object
            ->addInput('field_token', 'Lorem ipsum dolor sit amet')
            ->addRule('field_token', 'token');

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }

    public function testTokenErrorSessionTime()
    {
        $_SESSION[ 'token' ]      = 'Lorem ipsum dolor sit amet';
        $_SESSION[ 'token_time' ] = time() - 1000;

        $this->object
            ->addInput('field_token', 'Lorem ipsum dolor sit amet')
            ->addRule('field_token', 'token');

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }

    public function testTokenErrorSessionToken()
    {
        $_SESSION[ 'token' ]      = 'Lorem ipsum dolor sit amet';
        $_SESSION[ 'token_time' ] = time();

        $this->object
            ->addInput('field_token', 'error')
            ->addRule('field_token', 'token')
            ->isValid();

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }

    public function testUrl()
    {
        $this->object->setInputs([
            'field_url'              => 'http://localhost.fr',
            'field_not_url'          => 'not url',
            'field_url_required'     => 'http://localhost.fr',
            'field_url_not_required' => ''
        ])->setRules([
            'field_url'              => 'url',
            'field_not_url'          => '!url',
            'field_url_required'     => 'required|url',
            'field_url_not_required' => '!required|url'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_url'     => 'not url',
            'field_not_url' => 'http://localhost.fr'
        ])->setRules([
            'field_url'     => 'url',
            'field_not_url' => '!url'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testHtmlsc()
    {
        $this->object
            ->addInput('field_html', '<p>bonjour</p>')
            ->addRule('field_html', 'htmlsc:<p>')
            ->isValid();

        $this->assertAttributeSame([ 'field_html' => '&lt;p&gt;bonjour&lt;/p&gt;' ], 'inputs', $this->object);
    }

    /**
     * @expectedException \Exception
     */
    public function testHtmlscException()
    {
        $this->object
            ->addInput('field_html', 1)
            ->addRule('field_html', 'htmlsc:<p>')
            ->isValid();
    }

    public function testStripTags()
    {
        $this->object
            ->addInput('field_html', '<p>bonjour <a href="#">lien</a></p>')
            ->addRule('field_html', 'striptags:<p>')
            ->isValid();

        $this->assertAttributeSame([ 'field_html' => '<p>bonjour lien</p>' ], 'inputs', $this->object);
    }

    /**
     * @expectedException \Exception
     */
    public function testStripTagsException()
    {
        $this->object
            ->addInput('field_html', 1)
            ->addRule('field_html', 'striptags:<p>')
            ->isValid();
    }

    public function testCustomTest()
    {
        Validator::addTest('cube', new Cube());
        Validator::addTest('double', new DoubleR());
        $this->object->setInputs([
            'field_custom_cube'      => 4,
            'field_custom_not_cube'  => 2,
            'field_custom_multi'     => 8,
            'field_custom_not_multi' => 2
        ])->setRules([
            'field_custom_cube'      => 'cube:16',
            'field_custom_not_cube'  => '!cube:16',
            'field_custom_multi'     => 'double',
            'field_custom_not_multi' => '!double'
        ]);

        $this->assertTrue($this->object->isValid());
    }

    public function testCustomTestReturn()
    {
        Validator::addTest('cube', new Cube());
        $this->object->setInputs([
            'field_custom_cube'     => 5,
            'field_custom_not_cube' => 4
        ])->setRules([
            'field_custom_cube'     => 'cube:16',
            'field_custom_not_cube' => '!cube:16'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertEquals($this->object->getError('field_custom_cube.cube'), 'La valeur au cube de field_custom_cube n\'est pas égale à 4.');
        
        $this->object->setInputs([
            'field_custom_cube2'     => 4,
            'field_custom_not_cube2' => 2
        ])->setRules([
            'field_custom_cube2'     => 'cube:16',
            'field_custom_not_cube2' => '!cube:16'
        ]);

        $this->assertTrue($this->object->isValid());
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

class ObjectTest
{
    public function __toString()
    {
        return 'test content';
    }
}
