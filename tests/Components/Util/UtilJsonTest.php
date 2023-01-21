<?php

namespace Soosyze\Tests\Components\Util;

use Soosyze\Components\Util\Util;

class UtilJsonTest extends \PHPUnit\Framework\TestCase
{
    private const PATH = 'tests/Resources/Util/Json';

    private const FILE = 'data';

    private const PATH_FILE_GET = self::PATH . '/dataGet.json';

    private const PATH_FILE_CREATE = self::PATH . '/dataCreate.json';

    private const PATH_FILE_ERROR = self::PATH . '/fileError.json';

    public function testCreateJson(): void
    {
        $output = Util::createJson(self::PATH, 'dataCreate');
        $this->assertFileExists(self::PATH_FILE_CREATE);
        $this->assertTrue($output);

        $output = Util::createJson(self::PATH, 'dataCreate');
        $this->assertNull($output);

        if (file_exists(self::PATH_FILE_CREATE)) {
            unlink(self::PATH_FILE_CREATE);
        }
    }

    public function testSaveJson(): void
    {
        $output = Util::saveJson(self::PATH, self::FILE, ['key' => 'value']);
        $this->assertTrue($output);
    }

    public function testGetJsonExceptionContent(): void
    {
        fopen(self::PATH_FILE_ERROR, 'w+');

        $this->expectException(\Exception::class);
        Util::getJson(self::PATH_FILE_ERROR);

        if (file_exists(self::PATH_FILE_ERROR)) {
            unlink(self::PATH_FILE_ERROR);
        }
    }

    public function testGetJsonExceptionFile(): void
    {
        $this->expectException(\Exception::class);
        Util::getJson('error');
    }

    public function testGetJsonExceptionExtension(): void
    {
        $this->expectException(\Exception::class);
        Util::getJson(__FILE__);
    }

    public function testGetJson(): void
    {
        $this->assertEquals(['key' => 'value'], Util::getJson(self::PATH_FILE_GET));
    }
}
