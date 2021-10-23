<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\UploadedFile;

class UploadedFileRessourceTest extends \PHPUnit\Framework\TestCase
{
    use \Soosyze\Tests\Traits\ResourceTrait;

    /**
     * @var UploadedFile
     */
    protected $object;

    protected function setUp(): void
    {
        /* Créer un fichier pour le test */
        $stream = $this->streamFactory('test content ressource', 'w');

        $this->object = new UploadedFile($stream);
    }

    public function testGetStream(): void
    {
        $stream = $this->object->getStream();
        $this->assertEquals('test content ressource', (string) $stream);
        /* Si nous ne fermons pas le flux le fichier sera vérouillé pour le reste des opérations */
        $stream->close();
    }

    public function testMoveTo(): void
    {
        $targetPath = './moveTest.txt';

        $this->object->moveTo($targetPath);
        $this->assertFileExists($targetPath);
        unlink($targetPath);
    }

    public function testMoveExceptionMoved(): void
    {
        $targetPath = './error.txt';
        $this->object->moveTo($targetPath);
        unlink($targetPath);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The file has already been moved.');
        $this->object->moveTo($targetPath);
    }

    public function testMoveExceptionTarget(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Target is incorrect.');
        $this->object->moveTo(1);
    }

    public function testGetStreamException(): void
    {
        $targetPath = './error.txt';
        $this->object->moveTo($targetPath);
        unlink($targetPath);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The file has already been moved.');
        $this->object->getStream();
    }

    public function testGetSize(): void
    {
        $this->assertEquals(null, $this->object->getSize());
    }

    public function testGetError(): void
    {
        $this->assertEquals(0, $this->object->getError());
    }

    public function testGetClientFilename(): void
    {
        $this->assertEquals(null, $this->object->getClientFilename());
    }

    public function testGetClientMediaType(): void
    {
        $this->assertEquals(null, $this->object->getClientMediaType());
    }
}
