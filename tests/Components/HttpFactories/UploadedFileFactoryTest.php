<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Psr\Http\Message\UploadedFileInterface;
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

    protected function setUp(): void
    {
        $this->object = new UploadedFileFactory;
        $this->stream = new StreamFactory;
    }

    public function testCreateUploadedFile(): void
    {
        $stream     = $this->stream->createStream('test');
        $uploadFile = $this->object->createUploadedFile($stream);

        $this->assertInstanceOf(UploadedFileInterface::class, $uploadFile);
    }
}
