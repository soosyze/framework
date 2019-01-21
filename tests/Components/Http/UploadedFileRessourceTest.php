<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\UploadedFile;

class UploadedFileRessourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var UplaodeFile
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        /* Créer un fichier pour le test */
        $resource = fopen('php://temp', 'w');
        fwrite($resource, 'test content ressource');
        rewind($resource);

        $this->object = new UploadedFile($resource);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testGetStream()
    {
        $stream = $this->object->getStream();
        $this->assertEquals((string) $stream, 'test content ressource');
        /* Si nous ne fermons pas le flux le fichier sera vérouillé pour le reste des opérations */
        $stream->close();
    }

    public function testMoveTo()
    {
        $targetPath = './moveTest.txt';

        $this->object->moveTo($targetPath);
        $this->assertFileExists($targetPath);
        unlink($targetPath);
    }

    /**
     * @expectedException \Exception
     */
    public function testMoveExceptionMoved()
    {
        $targetPath = './error.txt';
        $this->object->moveTo($targetPath);
        unlink($targetPath);

        $this->object->moveTo($targetPath);
    }

    /**
     * @expectedException \Exception
     */
    public function testMoveExceptionTarget()
    {
        $this->object->moveTo(1);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetStreamException()
    {
        $targetPath = './error.txt';
        $this->object->moveTo($targetPath);
        unlink($targetPath);

        $this->object->getStream();
    }

    public function testGetSize()
    {
        $this->assertEquals($this->object->getSize(), null);
    }

    public function testGetError()
    {
        $this->assertEquals($this->object->getError(), 0);
    }

    public function testGetClientFilename()
    {
        $this->assertEquals($this->object->getClientFilename(), null);
    }

    public function testGetClientMediaType()
    {
        $this->assertEquals($this->object->getClientMediaType(), null);
    }
}
