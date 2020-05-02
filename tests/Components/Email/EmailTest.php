<?php

namespace Soosyze\Tests\Components\Email;

use Soosyze\Components\Email\Email;

class EmailTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Email
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Email;
    }

    public function testFiltreMail()
    {
        self::callMethod($this->object, 'filtreEmail', [ 'test@exemple.com' ]);
        $this->assertTrue(true);
    }

    /**
     * @expectedException \Exception
     */
    public function testFiltreMailException()
    {
        self::callMethod($this->object, 'filtreEmail', [ 'test@exemple' ]);
    }

    public function testGetHeaders()
    {
        $this->assertEquals($this->object->getHeaders(), [
            'mime-version'              => [ '1.0' ],
            'x-priority'                => [ '3' ],
            'content-type'              => [ 'text/plain; charset=UTF-8; format=flowed; delsp=yes' ]
        ]);
    }

    public function testGetHeader()
    {
        $this->assertEquals($this->object->getHeader('mime-version'), [ '1.0' ]);
    }

    public function testGetHeaderLine()
    {
        $this->assertEquals($this->object->getHeaderLine('mime-version'), '1.0');
    }

    public function testParseHeaders()
    {
        $eof     = "\r\n";
        $headers = 'mime-version: 1.0' . $eof
            . 'x-priority: 3' . $eof
            . 'content-type: text/plain; charset=UTF-8; format=flowed; delsp=yes' . $eof;

        $this->assertEquals($headers, $this->object->parseHeaders());
    }

    public function testTo()
    {
        $this->object = $this->object->to('test@exemple.com');
        $this->assertEquals([ 'test@exemple.com' ], $this->object->getHeader('to'));

        $this->object = $this->object->to('test@exemple.com', "\r\n" . 'Name');
        $this->assertEquals([ 'test@exemple.com', '"Name" <test@exemple.com>' ], $this->object->getHeader('to'));
    }

    public function testMessage()
    {
        $this->object->message('Lorem ipsum');
        $this->assertAttributeSame('Lorem ipsum', 'message', $this->object);
    }

    public function testSubject()
    {
        $this->object->subject('Lorem ipsum');
        $this->assertAttributeSame('Lorem ipsum', 'subject', $this->object);
    }

    public function testFrom()
    {
        $this->object->from('test@exemple.com');
        $this->assertEquals([ 'test@exemple.com' ], $this->object->getHeader('from'));

        $this->object->from('test@exemple.com', "\r\n" . 'Name');
        $this->assertEquals([ '"Name" <test@exemple.com>' ], $this->object->getHeader('from'));
    }

    public function testAddCc()
    {
        $this->object->addCc('test@exemple.com');
        $this->assertEquals([ 'test@exemple.com' ], $this->object->getHeader('cc'));

        $this->object->addCc('test@exemple.com', "\r\n" . 'Name');
        $this->assertEquals([ 'test@exemple.com', '"Name" <test@exemple.com>' ], $this->object->getHeader('cc'));
    }

    public function testAddBcc()
    {
        $this->object->addBcc('test@exemple.com');
        $this->assertEquals([ 'test@exemple.com' ], $this->object->getHeader('bcc'));

        $this->object->addBcc('test@exemple.com', "\r\n" . 'Name');
        $this->assertEquals([ 'test@exemple.com', '"Name" <test@exemple.com>' ], $this->object->getHeader('bcc'));
    }

    public function testReplayTo()
    {
        $this->object->replayTo('test@exemple.com');
        $this->assertEquals([ 'test@exemple.com' ], $this->object->getHeader('replay-to'));

        $this->object->replayTo('test@exemple.com', "\r\n" . 'Name');
        $this->assertEquals([ '"Name" <test@exemple.com>' ], $this->object->getHeader('replay-to'));
    }

    public function testIsHtml()
    {
        $this->object->isHtml();
        $this->assertEquals([ 'text/html; charset=UTF-8; format=flowed; delsp=yes' ], $this->object->getHeader('content-type'));
    }

    protected static function callMethod($obj, $name, array $args = [])
    {
        $class  = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }
}
