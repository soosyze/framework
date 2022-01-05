<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\UploadedFile;

class UploadedFileTest extends \PHPUnit\Framework\TestCase
{
    use \Soosyze\Tests\Traits\ResourceTrait;

    private const FILE = './test.txt';

    /**
     * @var UploadedFile
     */
    protected $object;

    protected function setUp(): void
    {
        $stream = $this->streamFileFactory(self::FILE, 'test content', 'w');
        fclose($stream);

        $this->object = new UploadedFile(self::FILE, 'file.txt', 1024, 'text/plain');
    }

    protected function tearDown(): void
    {
        /* Supprime le fichier du test */
        if (file_exists(self::FILE)) {
            unlink(self::FILE);
        }
    }

    /**
     * @dataProvider providerConstructException
     *
     * @param class-string<\Throwable> $exceptionClass
     */
    public function testConstructFileException(
        array $args,
        string $exceptionClass,
        string $exceptionMessage
    ): void {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);
        new UploadedFile(...$args);
    }

    public function providerConstructException(): \Generator
    {
        yield [
            [ 1 ],
            \InvalidArgumentException::class,
            'The file resource is not readable.'
        ];
        yield [
            [ '', null, null, null, 1000 ],
            \InvalidArgumentException::class,
            'The type of error is invalid.'
        ];
    }

    public function testCreateInvalidArgument(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        UploadedFile::create([]);
    }

    public function testGetStream(): void
    {
        $stream = $this->object->getStream();
        $this->assertEquals('test content', (string) $stream);
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

        $this->expectException(\Exception::class);
        $this->object->moveTo($targetPath);
    }

    public function testMoveExceptionTarget(): void
    {
        $this->expectException(\Exception::class);
        /** @phpstan-ignore-next-line */
        $this->object->moveTo(1);
    }

    public function testMoveExceptionFileError(): void
    {
        $upl = new UploadedFile('error');

        $this->expectException(\Exception::class);
        $upl->moveTo('test');
    }

    public function testGetStreamException(): void
    {
        $targetPath = './error.txt';
        $this->object->moveTo($targetPath);
        unlink($targetPath);

        $this->expectException(\Exception::class);
        $this->object->getStream();
    }

    public function testGetSize(): void
    {
        $this->assertEquals(1024, $this->object->getSize());
    }

    public function testGetError(): void
    {
        $this->assertEquals(0, $this->object->getError());
    }

    public function testGetClientFilename(): void
    {
        $this->assertEquals('file.txt', $this->object->getClientFilename());
    }

    public function testGetClientMediaType(): void
    {
        $this->assertEquals('text/plain', $this->object->getClientMediaType());
    }
}
