<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Psr\Http\Message\StreamInterface;
use Soosyze\Components\HttpFactories\StreamFactory;

class StreamFactoryTest extends \PHPUnit\Framework\TestCase
{
    use \Soosyze\Tests\Traits\ResourceTrait;

    /**
     * @var StreamFactory
     */
    protected $object;

    /**
     * @var string
     */
    protected $file = './testStreamFactory.txt';

    protected function setUp(): void
    {
        /* Créer un fichier pour le test */
        $stream = $this->streamFileFactory($this->file, 'test content', 'w');
        fclose($stream);

        $this->object = new StreamFactory;
    }

    protected function tearDown(): void
    {
        /* Supprime le fichier du test */
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public function testCreateStream(): void
    {
        $stream = $this->object->createStream('test');
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }

    public function testCreateStreamFromFile(): void
    {
        $stream = $this->object->createStreamFromFile($this->file);
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }

    public function testCreateStreamFromResource(): void
    {
        $resource = $this->getRessourceTemp();

        $stream   = $this->object->createStreamFromResource($resource);
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }
}
