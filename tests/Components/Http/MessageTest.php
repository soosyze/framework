<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Message;
use Soosyze\Components\Http\Stream;

class MessageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Message
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Message();
    }

    /**
     * @dataProvider providerWithProtocolVersionException
     *
     * @param mixed $version
     */
    public function testWithProtocolVersionValueException($version): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The specified protocol is invalid.');
        /** @phpstan-ignore-next-line */
        $this->object->withProtocolVersion($version);
    }

    public function providerWithProtocolVersionException(): \Generator
    {
        yield [ 1.1 ];
        yield [ '9.0' ];
    }

    public function testGetProtocolVersion(): void
    {
        $clone = $this->object->withProtocolVersion('2.0');
        $this->assertEquals($clone->getProtocolVersion(), '2.0');
    }

    public function testWithHeader(): void
    {
        $clone = $this->object->withHeader('location', [ 'http://www.foo.com' ]);
        $this->assertEquals([ 'location' => [ 'http://www.foo.com' ] ], $clone->getHeaders());

        $clone = $clone->withHeader('LOCATION', 'http://www.bar.com');
        $this->assertEquals([ 'LOCATION' => [ 'http://www.bar.com' ] ], $clone->getHeaders());
    }

    public function testGetHeaders(): void
    {
        $this->assertEquals([], $this->object->getHeaders());
        $clone = $this->object->withHeader('location', 'http://www.foo.com');
        $this->assertEquals([ 'location' => [ 'http://www.foo.com' ] ], $clone->getHeaders());
    }

    public function testHasHeader(): void
    {
        $this->assertFalse($this->object->hasHeader('location'));
        $clone = $this->object->withHeader('location', 'http://www.foo.com');

        $this->assertTrue($clone->hasHeader('location'));
        $this->assertTrue($clone->hasHeader('LOCATION'));
    }

    public function testGetHeader(): void
    {
        $this->assertEquals([], $this->object->getHeader('location'));
        $clone = $this->object->withHeader('location', 'http://www.foo.com');

        $this->assertEquals([ 'http://www.foo.com' ], $clone->getHeader('location'));
        $this->assertEquals([ 'http://www.foo.com' ], $clone->getHeader('LOCATION'));
    }

    public function testGetHeaderLine(): void
    {
        $clone = $this->object->withAddedHeader('location', 'http://www.foo.com/');
        $this->assertEquals(
            'http://www.foo.com/',
            $clone->getHeaderLine('location')
        );
        $this->assertEquals(
            'http://www.foo.com/',
            $clone->getHeaderLine('LOCATION')
        );

        $clone = $clone->withAddedHeader('location', 'http://www.bar.com/');
        $this->assertEquals(
            'http://www.foo.com/,http://www.bar.com/',
            $clone->getHeaderLine('location')
        );
        $this->assertEquals(
            'http://www.foo.com/,http://www.bar.com/',
            $clone->getHeaderLine('LOCATION')
        );
    }

    public function testWithAddedHeader(): void
    {
        $clone = $this->object->withAddedHeader('LOCATION', 'http://www.foo.com');
        $this->assertEquals([ 'http://www.foo.com' ], $clone->getHeader('location'));

        $clone = $clone->withAddedHeader('location', 'http://www.bar.com');
        $this->assertEquals(
            [ 'http://www.foo.com', 'http://www.bar.com' ],
            $clone->getHeader('location')
        );
    }

    public function testWithAddedHeaderMultiple(): void
    {
        $clone = $this->object
            ->withAddedHeader('location', 'http://www.foo.com')
            ->withAddedHeader('LOCATION', 'http://www.bar.com');

        $this->assertEquals(
            [ 'location' => [ 'http://www.foo.com', 'http://www.bar.com' ] ],
            $clone->getHeaders()
        );
    }

    public function testWithoutHeader(): void
    {
        $clone = $this->object
            ->withHeader('TestHeader1', 'ValueTest1')
            ->withHeader('TestHeader2', 'ValueTest2');

        $this->assertEquals(
            [ 'TestHeader1' => [ 'ValueTest1' ], 'TestHeader2' => [ 'ValueTest2' ] ],
            $clone->withoutHeader('ErrorHeader')->getHeaders()
        );

        $this->assertEquals(
            [ 'TestHeader2' => [ 'ValueTest2' ] ],
            $clone->withoutHeader('testheader1')->getHeaders()
        );
    }

    public function testWithBody(): void
    {
        $stream = new Stream;
        $clone  = $this->object->withBody($stream);

        $this->assertEquals($stream, $clone->getBody());
    }

    /**
     * @dataProvider providerInvalidHeaderArguments

     * @param mixed $name
     * @param mixed $value
     */
    public function testWithHeaderInvalidArguments(
        $name,
        $value,
        string $exceptionMessage
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);
        /** @phpstan-ignore-next-line */
        $this->object->withHeader($name, $value);
    }

    public function providerInvalidHeaderArguments(): \Generator
    {
        yield [ [], 'foo', 'Header name must be an RFC 7230 compatible string.'];
        yield [ '', '', 'Header name must be an RFC 7230 compatible string.' ];
        yield [ false, 'foo', 'Header name must be an RFC 7230 compatible string.' ];
        yield [ new \stdClass(), 'foo', 'Header name must be an RFC 7230 compatible string.' ];

        yield [ 'foo', [], 'Header values must be a string or an array of strings, empty array given.' ];

        yield [ 'foo', false, 'Header values must be RFC 7230 compatible strings.' ];
        yield [ 'foo', new \stdClass(), 'Header values must be RFC 7230 compatible strings.' ];
        yield [ 'foo', [ new \stdClass() ], 'Header values must be RFC 7230 compatible strings.' ];
    }
}
