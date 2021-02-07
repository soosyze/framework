<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Message;

class MessageTest extends \PHPUnit\Framework\TestCase
{
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Message();
    }

    public function testWithProtocolVersion()
    {
        $clone = $this->object->withProtocolVersion('2.0');
        $this->assertAttributeSame('2.0', 'protocolVersion', $clone);
    }

    /**
     * @expectedException \Exception
     */
    public function testWithProtocolVersionTypeException()
    {
        $this->object->withProtocolVersion(1.1);
    }

    /**
     * @expectedException \Exception
     */
    public function testWithProtocolVersionValueException()
    {
        $this->object->withProtocolVersion('9.0');
    }

    public function testGetProtocolVersion()
    {
        $clone = $this->object->withProtocolVersion('2.0');
        $this->assertEquals($clone->getProtocolVersion(), '2.0');
    }

    public function testWithHeader()
    {
        $clone = $this->object->withHeader('Location', 'http://www.example.com/');
        $this->assertAttributeSame([ 'Location' => [ 'http://www.example.com/' ] ], 'headers', $clone);

        $clone2 = $this->object->withHeader('Location', ['http://www.example.com/']);
        $this->assertAttributeSame([ 'Location' => [ 'http://www.example.com/' ] ], 'headers', $clone2);
    }

    public function testGetHeaders()
    {
        $this->assertArraySubset($this->object->getHeaders(), []);
        $clone = $this->object->withHeader('Location', 'http://www.example.com/');
        $this->assertArraySubset($clone->getHeaders(), [ 'Location' => [ 'http://www.example.com/' ] ]);
    }

    public function testHasHeader()
    {
        $this->assertFalse($this->object->hasHeader('Location'));
        $clone = $this->object->withHeader('Location', 'http://www.example.com/');
        $this->assertTrue($clone->hasHeader('Location'));
    }

    public function testGetHeader()
    {
        $this->assertArraySubset($this->object->getHeader('Location'), []);
        $clone = $this->object->withHeader('Location', 'http://www.example.com/');
        $this->assertArraySubset($clone->getHeader('location'), [ 'http://www.example.com/' ]);
    }

    public function testGetHeaderLine()
    {
        $clone = $this->object->withHeader('Location', 'http://www.foo.com/');

        $this->assertEquals($clone->getHeaderLine('location'), 'http://www.foo.com/');

        $this->assertEquals(
            $clone->withAddedHeader('Location', 'http://www.bar.com/')
                ->getHeaderLine('location'),
            'http://www.foo.com/,http://www.bar.com/'
        );
    }

    public function testWithAddedHeader()
    {
        $clone = $this->object->withAddedHeader('Location', 'http://www.example.com/');
        $this->assertArraySubset($clone->getHeader('location'), [ 'http://www.example.com/' ]);
    }

    public function testWithAddedHeaderMultiple()
    {
        $clone = $this->object
            ->withAddedHeader('Location', 'http://www.example.com/')
            ->withAddedHeader('Location', 'http://www.example.com/');
        $this->assertAttributeSame([
            'Location' => [ 'http://www.example.com/', 'http://www.example.com/' ]
            ], 'headers', $clone);
    }

    public function testWithoutHeader()
    {
        $clone = $this->object
            ->withHeader('TestHeader1', 'ValueTest1')
            ->withHeader('TestHeader2', 'ValueTest2');

        $cloneError = $clone->withoutHeader('ErrorHeader');
        $this->assertAttributeSame([
            'TestHeader1' => [ 'ValueTest1' ],
            'TestHeader2' => [ 'ValueTest2' ]
            ], 'headers', $cloneError);

        $cloneSuccess = $clone->withoutHeader('testheader1');
        $this->assertAttributeSame([
            'TestHeader2' => [ 'ValueTest2' ]
            ], 'headers', $cloneSuccess);
    }

    public function testWithBody()
    {
        $stream = new \Soosyze\Components\Http\Stream;
        $clone  = $this->object->withBody($stream);

        $this->assertAttributeSame($stream, 'body', $clone);
    }

    /**
     * @dataProvider getInvalidHeaderArguments
     * @expectedException \InvalidArgumentException
     */
    public function testWithHeaderInvalidArguments($name, $value)
    {
        $this->object->withHeader($name, $value);
    }

    public function getInvalidHeaderArguments()
    {
        return [
            [[], 'foo'],
            ['foo', []],
            ['', ''],
            ['foo', false],
            [false, 'foo'],
            ['foo', new \stdClass()],
            [new \stdClass(), 'foo'],
            ['foo', [new \stdClass()]]
        ];
    }
}
