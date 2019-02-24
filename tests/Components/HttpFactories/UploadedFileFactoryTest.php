<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Soosyze\Components\HttpFactories\StreamFactory;
use Soosyze\Components\HttpFactories\UploadedFileFactory;

class UploadedFileFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var UploadedFileFactory
     */
    protected $object;

    /**
     * @var StreamFactory
     */
    protected $stream;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new UploadedFileFactory;
        $this->stream = new StreamFactory;
    }

    public function testCreateUploadedFile()
    {
        $stream     = $this->stream->createStream('test');
        
//        $resource = fopen('php://temp', 'r+');
//        $stream   = $this->stream->createStreamFromResource($resource);
        
        $uploadFile = $this->object->createUploadedFile($stream);
        $this->assertInstanceOf('\Psr\Http\Message\UploadedFileInterface', $uploadFile);
    }
}
