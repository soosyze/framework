<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class TokenTest extends Rule
{
    public function testToken()
    {
        $_SESSION[ 'token' ]      = [
            'token'          => 'Lorem ipsum dolor sit amet',
            'token_required' => 'Lorem ipsum dolor sit amet'
        ];
        $_SESSION[ 'token_time' ] = [
            'token'          => time(),
            'token_required' => time()
        ];

        $this->object->setInputs([
            'token'              => 'Lorem ipsum dolor sit amet',
            'token_required'     => 'Lorem ipsum dolor sit amet',
            'token_not_required' => ''
        ])->setRules([
            'token'              => 'token',
            'token_required'     => 'required|token',
            'token_not_required' => '!required|token'
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
            ->addInput('field', 'error')
            ->addRule('field', 'token:error')
            ->isValid();
    }

    public function testTokenErrorSession()
    {
        @session_destroy();
        $this->object
            ->addInput('field', 'Lorem ipsum dolor sit amet')
            ->addRule('field', 'token');

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }

    public function testTokenErrorSessionTime()
    {
        $_SESSION[ 'token' ][ 'field' ]      = 'Lorem ipsum dolor sit amet';
        $_SESSION[ 'token_time' ][ 'field' ] = time() - 1000;

        $this->object
            ->addInput('field', 'Lorem ipsum dolor sit amet')
            ->addRule('field', 'token');

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }

    public function testTokenErrorSessionToken()
    {
        $_SESSION[ 'token' ][ 'field' ]      = 'Lorem ipsum dolor sit amet';
        $_SESSION[ 'token_time' ][ 'field' ] = time();

        $this->object
            ->addInput('field', 'error')
            ->addRule('field', 'token')
            ->isValid();

        $this->assertFalse($this->object->isValid());
        $this->assertCount(1, $this->object->getErrors());
    }
}
