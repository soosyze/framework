<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Stream;

class StreamTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var resource
     */
    protected $file = './testStream.txt';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        /* Créer un fichier pour le test */
        $stream = fopen($this->file, 'w');
        fwrite($stream, 'test content');
        fclose($stream);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        /* Supprime le fichier du test */
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public function streamFactory($mode = 'r+')
    {
        $stream = fopen('php://temp', $mode);
        fwrite($stream, 'test');
        rewind($stream);

        return $stream;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructException()
    {
        $body = new Stream([]);
    }

    public function testToString()
    {
        $body = new Stream();
        $this->assertEquals($body, '');

        $body = new Stream(null);
        $this->assertEquals($body, '');

        $body = new Stream(true);
        $this->assertEquals($body, '1');

        $body = new Stream('test');
        $this->assertEquals($body, 'test');

        $body = new Stream(1970);
        $this->assertEquals($body, '1970');

        $body = new Stream(1970.01);
        $this->assertEquals($body, '1970.01');

        $body = new Stream($this->streamFactory());
        $this->assertEquals($body, 'test');

        $body = new Stream('test');
        $body->detach();
        $this->assertEquals($body, '');
        
        $body = new Stream(new objetTest());
        $this->assertEquals($body, 'test');
    }
    
    public function testCreateStreamFromFile()
    {
        $stream = Stream::createStreamFromFile($this->file);

        $this->assertInstanceOf('\Psr\Http\Message\StreamInterface', $stream);
        $this->assertEquals($stream, 'test content');
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testCreateStreamFromFileRuntime()
    {
        Stream::createStreamFromFile('error');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateStreamFromFileInvalidArgument()
    {
        Stream::createStreamFromFile($this->file, 'error');
    }

    public function testDetach()
    {
        $stream       = fopen('php://temp', 'r+');
        $body         = new Stream($stream);
        $streamDetach = $body->detach();
        $this->assertEquals($streamDetach, $stream);
    }

    public function testGetSize()
    {
        $body = new Stream('test');
        $this->assertEquals($body->getSize(), 4);
        $body->detach();
        $this->assertNull($body->getSize());
        $body->close();
        $this->assertNull($body->getSize());
    }

    public function testTell()
    {
        $body = new Stream('test');
        $this->assertEquals($body->tell(), 0);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTellException()
    {
        $body = new Stream('test');
        $body->close();
        $body->tell();
    }

    public function testEof()
    {
        $body = new Stream('test');
        $this->assertFalse($body->eof());

        /* Va lire caractère par caractère jusqu'a arriver à la fin de la chaine. */
        while (!$body->eof()) {
            $body->read(1);
        }
        $this->assertTrue($body->eof());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEofException()
    {
        $body = new Stream('test');
        $body->close();
        $body->eof();
    }

    public function testIsSeekable()
    {
        $body = new Stream('test');
        $this->assertTrue($body->isSeekable());
        $body->detach();
        $this->assertFalse($body->isSeekable());
    }

    public function testSeek()
    {
        $body = new Stream('test');
        $body->seek(2);
        $this->assertEquals($body->read(4), 'st');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSeekException()
    {
        $body = new Stream('test');
        $body->seek(5);
    }

    public function testRewind()
    {
        $body = new Stream('test');
        $body->seek(2);
        $body->rewind();

        $this->assertEquals($body->read(4), 'test');
    }

    public function testIsWritable()
    {
        $body = new Stream('test');
        $this->assertTrue($body->isWritable());

        $body->detach();
        $this->assertFalse($body->isWritable());

        $body = new Stream($this->streamFactory('r'));
        $this->assertFalse($body->isWritable());
    }

    public function testWrite()
    {
        $body = new Stream();
        $body->write('test');
        $this->assertEquals($body, 'test');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testWriteException()
    {
        $body = new Stream();
        $body->close();
        $body->write('test');
    }

    public function testIsReadable()
    {
        $body = new Stream();
        $this->assertTrue($body->isReadable());

        $body->detach();
        $this->assertFalse($body->isReadable());

        /* Ouvre un flux en ecriture seule */
        $body = new Stream(fopen('php://stdout', 'a'));
        $this->assertFalse($body->isReadable());
    }

    public function testRead()
    {
        $body = new Stream('test');
        $read = $body->read(2);
        $this->assertEquals($read, 'te');

        $read = $body->read(2);
        $this->assertEquals($read, 'st');
    }

    public function testReadNumericLength()
    {
        $body = new Stream('test');
        $read = $body->read('1');
        $this->assertEquals($read, 't');
    }

    public function testReadZeroLength()
    {
        $body = new Stream('test');
        $read = $body->read(0);
        $this->assertEquals($read, '');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testReadLengthException()
    {
        $body = new Stream('test');
        $body->read(-1);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testReadStringLengthException()
    {
        $body = new Stream('test');
        $body->read('error');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testReadException()
    {
        $body = new Stream();
        $body->close();
        $body->read(4);
    }

    public function testGetContent()
    {
        $body = new Stream('test');
        $this->assertEquals($body->getContents(), 'test');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetContentException()
    {
        $body = new Stream('test');
        $body->close();
        $this->assertEquals($body->getContents(), 'test');
    }

    public function testGetMetadata()
    {
        $body = new Stream('test');
        $data = $body->getMetadata();
        $this->assertEquals($data, [
            'wrapper_type' => 'PHP',
            'stream_type'  => 'TEMP',
            'mode'         => 'w+b',
            'unread_bytes' => 0,
            'seekable'     => true,
            'uri'          => 'php://temp' ]);
    }

    public function testGetMetadataKey()
    {
        $body = new Stream('test');
        $data = $body->getMetadata('uri');
        $this->assertEquals($data, 'php://temp');
    }

    public function testGetMetadataErrorKey()
    {
        $body = new Stream('test');
        $data = $body->getMetadata('error');
        $this->assertNull($data);
    }
}

class objetTest
{
    public function __toString()
    {
        return 'test';
    }
}
