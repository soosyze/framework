<?php

namespace Soosyze\Tests\Components\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Soosyze\Components\Http\Stream;
use Soosyze\Tests\Traits\ResourceTrait;

class StreamTest extends \PHPUnit\Framework\TestCase
{
    use ResourceTrait;

    private const FILE = './testStream.txt';

    protected function setUp(): void
    {
        $stream = $this->streamFileFactory(self::FILE, 'test content', 'w');
        fclose($stream);
    }

    protected function tearDown(): void
    {
        if (file_exists(self::FILE)) {
            unlink(self::FILE);
        }
    }

    /**
     * @dataProvider providerConstructException
     *
     * @param mixed                    $mixed
     * @param class-string<\Throwable> $exceptionClass
     */
    public function testConstructException(
        $mixed,
        string $exceptionClass,
        string $exceptionMessage
    ): void {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);
        new Stream($mixed);
    }

    public function providerConstructException(): \Generator
    {
        yield [
            [],
            \InvalidArgumentException::class,
            'Stream must be a resource.'
        ];
    }

    /**
     * @dataProvider providerToString
     */
    public function testToString(string $expectedStream, Stream $stream): void
    {
        $this->assertEquals($expectedStream, $stream);
    }

    public function providerToString(): \Generator
    {
        yield [ '', new Stream() ];
        yield [ '', new Stream(null) ];
        yield [ '1', new Stream(true) ];
        yield [ 'test', new Stream('test') ];
        yield [ '1970', new Stream(1970) ];
        yield [ '1970.01', new Stream(1970.01) ];
        yield [ 'test', new Stream($this->streamFactory('test')) ];
        yield [ 'test', new Stream(new ObjetTest()) ];
    }

    public function testCreateStreamFromFile(): void
    {
        $stream = Stream::createStreamFromFile(self::FILE);

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals('test content', $stream);
    }

    public function testCreateStreamFromFileRuntime(): void
    {
        $this->expectException(RuntimeException::class);
        Stream::createStreamFromFile('error');
    }

    public function testCreateStreamFromFileInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Stream::createStreamFromFile(self::FILE, 'error');
    }

    public function testDetach(): void
    {
        $stream = fopen('php://temp', 'r+');
        $body   = new Stream($stream);

        $this->assertEquals($stream, $body->detach());
    }

    public function testGetSize(): void
    {
        $body = new Stream('test');
        $this->assertEquals(4, $body->getSize());

        $body->detach();
        $this->assertNull($body->getSize());

        $body->close();
        $this->assertNull($body->getSize());
    }

    public function testTell(): void
    {
        $body = new Stream('test');
        $this->assertEquals(0, $body->tell());
    }

    public function testTellException(): void
    {
        $body = new Stream('test');
        $body->close();

        $this->expectException(RuntimeException::class);
        $body->tell();
    }

    public function testEof(): void
    {
        $body = new Stream('test');
        $this->assertFalse($body->eof());

        /* Va lire caractère par caractère jusqu'a arriver à la fin de la chaine. */
        while (!$body->eof()) {
            $body->read(1);
        }
        $this->assertTrue($body->eof());
    }

    public function testEofException(): void
    {
        $body = new Stream('test');
        $body->close();

        $this->expectException(RuntimeException::class);
        $body->eof();
    }

    public function testIsSeekable(): void
    {
        $body = new Stream('test');
        $this->assertTrue($body->isSeekable());

        $body->detach();
        $this->assertFalse($body->isSeekable());
    }

    public function testSeek(): void
    {
        $body = new Stream('test');
        $body->seek(2);
        $this->assertEquals('st', $body->read(4));
    }

    public function testSeekException(): void
    {
        $body = new Stream('test');

        $this->expectException(RuntimeException::class);
        $body->seek(5);
    }

    public function testRewind(): void
    {
        $body = new Stream('test');
        $body->seek(2);
        $body->rewind();

        $this->assertEquals('test', $body->read(4));
    }

    public function testIsWritable(): void
    {
        $body = new Stream('test');
        $this->assertTrue($body->isWritable());

        $body->detach();
        $this->assertFalse($body->isWritable());

        $body = new Stream($this->streamFactory('test', 'r'));
        $this->assertFalse($body->isWritable());
    }

    public function testWrite(): void
    {
        $body = new Stream();
        $body->write('test');
        $this->assertEquals('test', $body);
    }

    public function testWriteException(): void
    {
        $body = new Stream();
        $body->close();

        $this->expectException(RuntimeException::class);
        $body->write('test');
    }

    public function testIsReadable(): void
    {
        $body = new Stream();
        $this->assertTrue($body->isReadable());

        $body->detach();
        $this->assertFalse($body->isReadable());

        /* Ouvre un flux en ecriture seule */
        $body = new Stream(fopen('php://stdout', 'a'));
        $this->assertFalse($body->isReadable());
    }

    public function testRead(): void
    {
        $body = new Stream('test');

        $this->assertEquals('te', $body->read(2));
        $this->assertEquals('st', $body->read(2));
    }

    public function testReadNumericLength(): void
    {
        $body = new Stream('test');
        $this->assertEquals('t', $body->read(1));
    }

    public function testReadZeroLength(): void
    {
        $body = new Stream('test');
        $this->assertEquals('', $body->read(0));
    }

    public function testReadLengthException(): void
    {
        $body = new Stream('test');

        $this->expectException(RuntimeException::class);
        $body->read(-1);
    }

    public function testReadStringLengthException(): void
    {
        $body = new Stream('test');

        $this->expectException(RuntimeException::class);
        $body->read('error');
    }

    public function testReadException(): void
    {
        $body = new Stream();
        $body->close();

        $this->expectException(RuntimeException::class);
        $body->read(4);
    }

    public function testGetContent(): void
    {
        $body = new Stream('test');
        $this->assertEquals($body->getContents(), 'test');
    }

    public function testGetContentException(): void
    {
        $body = new Stream('test');
        $body->close();

        $this->expectException(RuntimeException::class);
        $body->getContents();
    }

    /**
     * @dataProvider providerGetMetadata
     * @param string|null $expectedMeta
     */
    public function testGetMetadataKey($expectedMeta, ?string $key): void
    {
        $body = new Stream('test');

        $this->assertEquals($expectedMeta, $body->getMetadata($key));
    }

    public function providerGetMetadata(): \Generator
    {
        yield [ 'php://temp', 'uri' ];
        yield [ null, 'error' ];
        yield [
            [
                'wrapper_type' => 'PHP',
                'stream_type'  => 'TEMP',
                'mode'         => 'w+b',
                'unread_bytes' => 0,
                'seekable'     => true,
                'uri'          => 'php://temp'
            ],
            null
        ];
    }
}

class ObjetTest
{
    public function __toString(): string
    {
        return 'test';
    }
}
