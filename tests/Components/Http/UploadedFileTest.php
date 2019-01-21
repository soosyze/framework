<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\UploadedFile;

class UploadedFileTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var UplaodeFile
     */
    protected $object;

    /**
     * @var resource
     */
    protected $file = './test.txt';

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

        $this->object = new UploadedFile($this->file, 'file.txt', 1024, 'text/plain');
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

    public function testConstruct()
    {
        $this->assertAttributeSame($this->file, 'file', $this->object);
        $this->assertAttributeSame('file.txt', 'name', $this->object);
        $this->assertAttributeSame(1024, 'size', $this->object);
        $this->assertAttributeSame('text/plain', 'type', $this->object);
        $this->assertAttributeSame(0, 'error', $this->object);
    }

    /**
     * @expectedException \Exception
     */
    public function testConstructFileException()
    {
        new UploadedFile(1);
    }

    /**
     * @expectedException \Exception
     */
    public function testConstructNameException()
    {
        new UploadedFile('', 1);
    }

    /**
     * @expectedException \Exception
     */
    public function testConstructSizeException()
    {
        new UploadedFile('', null, '1');
    }

    /**
     * @expectedException \Exception
     */
    public function testConstructTypeException()
    {
        new UploadedFile('', null, null, 1);
    }

    /**
     * @expectedException \Exception
     */
    public function testConstructErrorException()
    {
        $upFile = new UploadedFile('', null, null, null, 'bonjour');
    }

    public function testCreate()
    {
        $upFile = UploadedFile::create([
                'tmp_name' => $this->file,
                'name'     => 'file.txt',
                'size'     => 1024,
                'type'     => 'text/plain',
                'error'    => 0
        ]);

        $this->assertAttributeSame($this->file, 'file', $upFile);
        $this->assertAttributeSame('file.txt', 'name', $upFile);
        $this->assertAttributeSame(1024, 'size', $upFile);
        $this->assertAttributeSame('text/plain', 'type', $upFile);
        $this->assertAttributeSame(0, 'error', $upFile);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidArgument()
    {
        UploadedFile::create([]);
    }
    
    public function testGetStream()
    {
        $stream = $this->object->getStream();
        $this->assertEquals((string) $stream, 'test content');
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
    public function testMoveExceptionFileError()
    {
        $upl = new UploadedFile('error');
        $upl->moveTo('test');
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
        $this->assertEquals($this->object->getSize(), 1024);
    }

    public function testGetError()
    {
        $this->assertEquals($this->object->getError(), 0);
    }

    public function testGetClientFilename()
    {
        $this->assertEquals($this->object->getClientFilename(), 'file.txt');
    }

    public function testGetClientMediaType()
    {
        $this->assertEquals($this->object->getClientMediaType(), 'text/plain');
    }
}
