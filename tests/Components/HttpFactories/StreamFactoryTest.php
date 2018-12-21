<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Soosyze\Components\HttpFactories\StreamFactory;

class StreamFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var StreamFactory
     */
    protected $object;

    /**
     * @var resource
     */
    protected $file = './testStreamFactory.txt';

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

        $this->object = new StreamFactory;
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

    public function testCreateStream()
    {
        $stream = $this->object->createStream('test');
        $this->assertInstanceOf('\Psr\Http\Message\StreamInterface', $stream);
    }

    public function testCreateStreamFromFile()
    {
        $stream = $this->object->createStreamFromFile($this->file);
        $this->assertInstanceOf('\Psr\Http\Message\StreamInterface', $stream);
    }

    public function testCreateStreamFromResource()
    {
        $resource = fopen('php://temp', 'r+');
        $stream   = $this->object->createStreamFromResource($resource);
        $this->assertInstanceOf('\Psr\Http\Message\StreamInterface', $stream);
    }
}
