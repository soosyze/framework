<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Message;

class MessageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testWithProtocolVersion()
    {
        $msg   = new Message();
        $clone = $msg->withProtocolVersion('2.0');
        $this->assertAttributeSame('2.0', 'protocolVersion', $clone);
    }

    /**
     * @expectedException \Exception
     */
    public function testWithProtocolVersionTypeException()
    {
        $msg   = new Message();
        $clone = $msg->withProtocolVersion(1.1);
    }

    /**
     * @expectedException \Exception
     */
    public function testWithProtocolVersionValueException()
    {
        $msg = new Message();
        $msg->withProtocolVersion('9.0');
    }

    public function testGetProtocolVersion()
    {
        $msg   = new Message();
        $clone = $msg->withProtocolVersion('2.0');
        $this->assertEquals($clone->getProtocolVersion(), '2.0');
    }

    public function testWithHeader()
    {
        $msg   = new Message();
        $clone = $msg->withHeader('Location', 'http://www.example.com/');
        $this->assertAttributeSame([ 'location' => [ 'http://www.example.com/' ] ], 'headers', $clone);
        
        $msg2   = new Message();
        $clone2 = $msg2->withHeader('Location', ['http://www.example.com/']);
        $this->assertAttributeSame([ 'location' => [ 'http://www.example.com/' ] ], 'headers', $clone2);
    }

    public function testGetHeaders()
    {
        $msg   = new Message();
        $this->assertArraySubset($msg->getHeaders(), []);
        $clone = $msg->withHeader('Location', 'http://www.example.com/');
        $this->assertArraySubset($clone->getHeaders(), [ 'location' => [ 'http://www.example.com/' ] ]);
    }

    public function testHasHeader()
    {
        $msg   = new Message();
        $this->assertFalse($msg->hasHeader('Location'));
        $clone = $msg->withHeader('Location', 'http://www.example.com/');
        $this->assertTrue($clone->hasHeader('Location'));
    }

    public function testGetHeader()
    {
        $msg   = new Message();
        $this->assertArraySubset($msg->getHeader('Location'), []);
        $clone = $msg->withHeader('Location', 'http://www.example.com/');
        $this->assertArraySubset($clone->getHeader('location'), [ 'http://www.example.com/' ]);
    }

    public function testGetHeaderLine()
    {
        $msg   = new Message();
        $clone = $msg->withHeader('Location', 'http://www.foo.com/');

        $this->assertEquals($clone->getHeaderLine('location'), 'http://www.foo.com/');

        $this->assertEquals(
            $clone->withAddedHeader('Location', 'http://www.bar.com/')
                ->getHeaderLine('location'),
            'http://www.foo.com/,http://www.bar.com/'
        );
    }

    public function testWithAddedHeader()
    {
        $msg   = new Message();
        $clone = $msg->withAddedHeader('Location', 'http://www.example.com/');
        $this->assertArraySubset($clone->getHeader('Location'), [ 'http://www.example.com/' ]);
    }

    public function testWithAddedHeaderMultiple()
    {
        $msg   = new Message();
        $clone = $msg
            ->withAddedHeader('Location', 'http://www.example.com/')
            ->withAddedHeader('Location', 'http://www.example.com/');
        $this->assertAttributeSame([ 'location' =>
            [
                'http://www.example.com/',
                'http://www.example.com/'
            ]
            ], 'headers', $clone);
    }

    public function testWithoutHeader()
    {
        $msg   = new Message();
        $clone = $msg
            ->withHeader('TestHeader1', 'ValueTest1')
            ->withHeader('TestHeader2', 'ValueTest2');

        $cloneError = $clone->withoutHeader('ErrorHeader');
        $this->assertAttributeSame([
            'testheader1' => [ 'ValueTest1' ],
            'testheader2' => [ 'ValueTest2' ]
            ], 'headers', $cloneError);

        $cloneSuccess = $clone->withoutHeader('TestHeader1');
        $this->assertAttributeSame([
            'testheader2' => [ 'ValueTest2' ]
            ], 'headers', $cloneSuccess);
    }

    public function testWithBody()
    {
        $msg    = new Message();
        $stream = new \Soosyze\Components\Http\Stream;
        $clone  = $msg->withBody($stream);

        $this->assertAttributeSame($stream, 'body', $clone);
    }
}
