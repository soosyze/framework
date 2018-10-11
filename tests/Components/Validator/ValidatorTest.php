<?php

namespace Soosyze\Tests\Components\Form;

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
//        @session_start();
        $this->object = new Validator;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @expectedException Exception
     */
    public function testValidException()
    {
        $this->object
            ->addInput('field_between', 4)
            ->addRule('field_between', 'exception')
            ->isValid();
    }

    public function testValidAlphaNum()
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
    }

    public function testValidAlphaNumText()
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
    }

    public function testValidArray()
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
    }

    public function testValidBetween()
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
    }

    /**
     * @expectedException Exception
     */
    public function testValidBetweenMissingException()
    {
        $this->object
            ->addInput('field_between', 4)
            ->addRule('field_between', 'between')
            ->isValid();
    }

    /**
     * @expectedException Exception
     */
    public function testValidBetweenExceptionTypeMin()
    {
        $this->object
            ->addInput('field_between', 4)
            ->addRule('field_between', 'between:error,5')
            ->isValid();
    }

    /**
     * @expectedException Exception
     */
    public function testValidBetweenExceptionTypeMax()
    {
        $this->object
            ->addInput('field_between', 4)
            ->addRule('field_between', 'between:1,error')
            ->isValid();
    }

    /**
     * @expectedException Exception
     */
    public function testValidBetweenMinUpperMax()
    {
        $this->object
            ->addInput('field_between', 4)
            ->addRule('field_between', 'between:10,1')
            ->isValid();
    }

    /**
     * @expectedException Exception
     */
    public function testValidBetweenExceptionValue()
    {
        $this->object
            ->addInput('field_between', fopen('php://temp', 'r+'))
            ->addRule('field_between', 'between:1,10')
            ->isValid();
    }

    public function testValidBool()
    {
        $this->object->setInputs([
            'field_bool'              => true,
            'field_bool_int'          => 1,
            'field_bool_texr'         => 'on',
            'field_not_bool_text'     => 'not bool',
            'field_not_bool_int'      => 10,
            'field_not_bool_array'    => [ 1, 2 ],
            'field_bool_required'     => true,
            'field_bool_not_required' => ''
        ])->setRules([
            'field_bool'              => 'bool',
            'field_bool_int'          => 'bool',
            'field_bool_texr'         => 'bool',
            'field_not_bool_text'     => '!bool',
            'field_not_bool_int'      => '!bool',
            'field_not_bool_array'    => '!bool',
            'field_bool_required'     => 'required|bool',
            'field_bool_not_required' => '!required|bool'
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
    }

    public function testValidDate()
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
    }

    public function testValidDateFormat()
    {
        $this->object->setInputs([
            'field_date'              => '10/01/1994',
            'field_not_date'          => '1994/01/10',
            'field_date_required'     => '10/01/1994',
            'field_date_not_required' => ''
        ])->setRules([
            'field_date'              => 'dateformat:j/n/Y',
            'field_not_date'          => '!dateformat:j/n/Y',
            'field_date_required'     => 'required|dateformat:j/n/Y',
            'field_date_not_required' => '!required|dateformat:j/n/Y',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_date_error' => 'not date',
            'field_date'       => '1994/10/01',
            'field_not_date'   => '10/01/1994'
        ])->setRules([
            'field_date_error' => 'dateformat:j/n/Y',
            'field_date'       => 'dateformat:j/n/Y',
            'field_not_date'   => '!dateformat:j/n/Y'
        ]);

        $this->assertFalse($this->object->isValid());
    }

    public function testValidDateAfter()
    {
        $this->object->setInputs([
            'field_date'              => '10/01/1994',
            'field_not_date'          => '10/01/1994',
            'field_date_required'     => '10/01/1994',
            'field_date_not_required' => ''
        ])->setRules([
            'field_date'              => 'dateafter:10/02/1994',
            'field_not_date'          => '!dateafter:09/01/1994',
            'field_date_required'     => 'required|dateafter:10/02/1994',
            'field_date_not_required' => '!required|dateafter:10/02/1994',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_date_error'  => 'not date',
            'field_date_error2' => '10/01/1994',
            'field_date'        => '10/01/1994',
            'field_not_date'    => '09/01/1994',
        ])->setRules([
            'field_date_error'  => 'dateafter:10/01/1994',
            'field_date_error2' => 'dateafter:error',
            'field_date'        => 'dateafter:10/01/1994',
            'field_not_date'    => '!dateafter:10/01/1994',
        ]);

        $this->assertFalse($this->object->isValid());
    }

    public function testValidDateBefore()
    {
        $this->object->setInputs([
            'field_date'              => '10/01/1994',
            'field_not_date'          => '10/01/1994',
            'field_date_required'     => '10/01/1994',
            'field_date_not_required' => ''
        ])->setRules([
            'field_date'              => 'datebefore:09/01/1994',
            'field_not_date'          => '!datebefore:10/02/1994',
            'field_date_required'     => 'required|datebefore:09/01/1994',
            'field_date_not_required' => '!required|datebefore:09/01/1994',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_date_error'  => 'not date',
            'field_date_error2' => '10/01/1994',
            'field_date'        => '10/01/1994',
            'field_not_date'    => '10/01/1994',
        ])->setRules([
            'field_date_error'  => 'datebefore:10/01/1994',
            'field_date_error2' => 'datebefore:error',
            'field_date'        => 'datebefore:11/01/1994',
            'field_not_date'    => '!datebefore:09/01/1994'
        ]);

        $this->assertFalse($this->object->isValid());
    }

    public function testValidDir()
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
    }

    public function testValidEqual()
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
    }

    public function testValidFile()
    {
        $this->object->setInputs([
            'field_file'              => __DIR__ . '/ValidatorTest.php',
            'field_not_file'          => __DIR__ . '/noFichier.php',
            'field_file_required'     => __DIR__ . '/ValidatorTest.php',
            'field_file_not_required' => '',
        ])->setRules([
            'field_file'              => 'file',
            'field_not_file'          => '!file',
            'field_file_required'     => 'required|file',
            'field_file_not_required' => '!required|file',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_file'     => __DIR__ . '/noFichier.php',
            'field_not_file' => __DIR__ . '/ValidatorTest.php'
        ])->setRules([
            'field_file'     => 'file',
            'field_not_file' => '!file'
        ]);

        $this->assertFalse($this->object->isValid());
    }

    public function testValidFloat()
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
    }

    public function testValidEmail()
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
    }

    public function testValidIp()
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
    }

    public function testValidInt()
    {
        $this->object->setInputs([
            'field_int'              => 10,
            'field_not_int_text'     => 'not int',
            'field_not_int_float'    => 10.1,
            'field_not_int_array'    => [ 1, 2 ],
            'field_int_required'     => 10,
            'field_int_not_required' => ''
        ])->setRules([
            'field_int'              => 'int',
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
    }

    public function testValidJson()
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
            'field_json_not_required' => '!required|jsonF'
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
    }

    public function testValidInArray()
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
    }

    public function testValidMax()
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
            /* Entier */
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
    }

    /**
     * @expectedException Exception
     */
    public function testValidMaxExceptionMin()
    {
        $this->object
            ->addInput('field_text_max', 4)
            ->addRule('field_text_max', 'max:error')
            ->isValid();
    }

    /**
     * @expectedException Exception
     */
    public function testValidMaxExceptionValue()
    {
        $this->object
            ->addInput('field_text_max', fopen('php://temp', 'r+'))
            ->addRule('field_text_max', 'max:5')
            ->isValid();
    }

    public function testValidMin()
    {
        $this->object->setInputs([
            /* Text */
            'field_text_min'              => 'Lorem ipsum',
            'field_not_text_min'          => 'Lore',
            'field_text_min_required'     => 'Lorem ipsum',
            'field_text_min_not_required' => '',
            /* Entier */
            'field_int_min'               => 5,
            'field_not_int_min'           => 4,
            'field_int_min_required'      => 5,
            'field_int_min_not_required'  => '',
            /* Tableau */
            'field_array_min'             => [ 1, 2, 3, 4, 5 ],
            'field_not_array_min'         => [ 1, 2, 3, 4 ]
        ])->setRules([
            /* Text */
            'field_text_min'              => 'min:5',
            'field_not_text_min'          => '!min:5',
            'field_text_min_required'     => 'required|min:5',
            'field_text_min_not_required' => '!required|min:5',
            /* Entier */
            'field_int_min'               => 'min:5',
            'field_not_int_min'           => '!min:5',
            'field_int_min_required'      => 'required|min:5',
            'field_int_min_not_required'  => '!required|min:5',
            /* Entier */
            'field_array_min'             => 'min:5',
            'field_not_array_min'         => '!min:5'
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
    }

    /**
     * @expectedException Exception
     */
    public function testValidMinExceptionMin()
    {
        $this->object
            ->addInput('field_text_min', 4)
            ->addRule('field_text_min', 'min:error')
            ->isValid();
    }

    /**
     * @expectedException Exception
     */
    public function testValidMinExceptionValue()
    {
        $this->object
            ->addInput('field_text_min', fopen('php://temp', 'r+'))
            ->addRule('field_text_min', 'min:5')
            ->isValid();
    }

    public function testValidRegex()
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
    }

    public function testValidSlug()
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
    }

    public function testValidString()
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
            'field_not_text_array_error' => [ 1, 2 ]
        ])->setRules([
            'field_text_error'           => 'string',
            'field_text_required_error'  => 'required|string',
            'field_not_text_int_error'   => 'string',
            'field_not_text_float_error' => 'string',
            'field_not_text_array_error' => 'string'
        ]);

        $this->assertFalse($this->object->isValid());
    }

    public function testValidToken()
    {
        $_SESSION[ 'token' ]      = 'Lorem ipsum dolor sit amet';
        $_SESSION[ 'token_time' ] = time();

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
     * @expectedException Exception
     */
    public function testValidTokenException()
    {
        $_SESSION[ 'token' ]      = 'Lorem ipsum dolor sit amet';
        $_SESSION[ 'token_time' ] = time();

        $this->object
            ->addInput('field_token', 'error')
            ->addRule('field_token', 'token:error')
            ->isValid();
    }

    public function testValidTokenErrorSession()
    {
        @session_destroy();
        $this->object
            ->addInput('field_token', 'Lorem ipsum dolor sit amet')
            ->addRule('field_token', 'token');

        $this->assertFalse($this->object->isValid());
    }

    public function testValidTokenErrorSessionTime()
    {
        $_SESSION[ 'token' ]      = 'Lorem ipsum dolor sit amet';
        $_SESSION[ 'token_time' ] = time() - 1000;

        $this->object
            ->addInput('field_token', 'Lorem ipsum dolor sit amet')
            ->addRule('field_token', 'token');

        $this->assertFalse($this->object->isValid());
    }

    public function testValidTokenErrorSessionToken()
    {
        $_SESSION[ 'token' ]      = 'Lorem ipsum dolor sit amet';
        $_SESSION[ 'token_time' ] = time();

        $this->object
            ->addInput('field_token', 'error')
            ->addRule('field_token', 'token')
            ->isValid();

        $this->assertFalse($this->object->isValid());
    }

    public function testValidUrl()
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
    }

    public function testValideHtmlsc()
    {
        $this->object
            ->addInput('field_html', '<p>bonjour</p>')
            ->addRule('field_html', 'htmlsc:<p>')
            ->isValid();

        $this->assertAttributeSame([ 'field_html' => '&lt;p&gt;bonjour&lt;/p&gt;' ], 'inputs', $this->object);
    }

    /**
     * @expectedException Exception
     */
    public function testValideHtmlscException()
    {
        $this->object
            ->addInput('field_html', 1)
            ->addRule('field_html', 'htmlsc:<p>')
            ->isValid();
    }

    public function testValideStripTags()
    {
        $this->object
            ->addInput('field_html', '<p>bonjour <a href="#">lien</a></p>')
            ->addRule('field_html', 'striptags:<p>')
            ->isValid();

        $this->assertAttributeSame([ 'field_html' => '<p>bonjour lien</p>' ], 'inputs', $this->object);
    }

    /**
     * @expectedException Exception
     */
    public function testValideStripTagsException()
    {
        $this->object
            ->addInput('field_html', 1)
            ->addRule('field_html', 'striptags:<p>')
            ->isValid();
    }

    public function testCustomTest()
    {
        Validator::addTest('cube', function ($key, $value, $multi, $not = true) {
            if ($value * $value != $multi && $not) {
                return ['key' => $key, 'value' => $value, 'msg' => 'La valeur au cube de %s n\'est pas égale à 4.'];
            } elseif ($value * $value == $multi && !$not) {
                return ['key' => $key, 'value' => $value, 'msg' => 'La valeur au cube de %s ne doit pas être égale à 4.'];
            }
        });
        Validator::addTest('double', function ($key, $value, $not = true) {
            if ($value * 2 != 16 && $not) {
                return ['key' => $key, 'value' => $value, 'msg' => 'Le double de la valeur de %s n\'est pas égale à 16.'];
            } elseif ($value * 2 == 16 && !$not) {
                return ['key' => $key, 'value' => $value, 'msg' => 'Le double de la valeur de %s ne doit pas être égale à 16.'];
            }
        });
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
        Validator::addTest('cube', function ($key, $value, $multi, $not = true) {
            if ($value * $value != $multi && $not) {
                return ['key' => $key, 'value' => $value, 'msg' => 'La valeur au cube de %s n\'est pas égale à 4.'];
            } elseif ($value * $value == $multi && !$not) {
                return ['key' => $key, 'value' => $value, 'msg' => 'La valeur au cube de %s ne doit pas être égale à 4.'];
            }
        });
        $this->object->setInputs([
            'field_custom_cube'     => 5,
            'field_custom_not_cube' => 4
        ])->setRules([
            'field_custom_cube'     => 'cube:16',
            'field_custom_not_cube' => '!cube:16'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertEquals($this->object->getError('field_custom_cube.cube'), 'La valeur au cube de field_custom_cube n\'est pas égale à 4.');
    }

    public function testCustomMessage()
    {
        $this->object->setInputs([
            'field' => 'hello world !'
        ])->setRules([
            'field' => '!string'
        ])->setMessages([
            'field.string' => 'My message custom for %s !'
        ]);

        $this->object->isValid();

        $this->assertEquals($this->object->getError('field.string'), 'My message custom for field !');
    }
}
