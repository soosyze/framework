<?php

namespace Soosyze\Test;

use Soosyze\Components\Util\Util;

/**
 * @requires extension json
 * @coversDefaultClass \Soosyze\Components\Util\Util
 */
class UtilTest extends \PHPUnit\Framework\TestCase
{
    public static $path = __DIR__ . DIRECTORY_SEPARATOR . 'build';

    public static $file = 'file';

    public static $pathFile = __DIR__ . '/build/file.json';

    public static $pathFileError = __DIR__ . '/build/fileError.json';

    public static function tearDownAfterClass()
    {
        if (file_exists(self::$pathFile)) {
            unlink(self::$pathFile);
        }
        if (file_exists(self::$pathFileError)) {
            unlink(self::$pathFileError);
        }
        if (is_dir(self::$path)) {
            rmdir(self::$path);
        }
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testCreateJson()
    {
        $output = Util::createJson(self::$path, self::$file);
        $this->assertFileExists(self::$pathFile);
        $this->assertTrue($output);

        $output = Util::createJson(self::$path, self::$file);
        $this->assertNull($output);
    }

    public function testSaveJson()
    {
        $output = Util::saveJson(self::$path, self::$file, [ 'key' => 'value' ]);
        $this->assertTrue($output);
    }

    /**
     * @expectedException Exception
     */
    public function testGetJsonExceptionCentent()
    {
        fopen(self::$pathFileError, 'w+');
        Util::getJson(self::$pathFileError);
    }

    /**
     * @expectedException Exception
     */
    public function testGetJsonExceptionFile()
    {
        Util::getJson('error');
    }

    /**
     * @expectedException Exception
     */
    public function testGetJsonExceptionExtension()
    {
        Util::getJson(__DIR__ . '/UtilTest.php');
    }

    public function testGetJson()
    {
        $output = Util::getJson(self::$pathFile);
        $this->assertArraySubset([ 'key' => 'value' ], $output);
    }

    public function testGetFileExtension()
    {
        $output = Util::getFileExtension(__DIR__ . '/UtilTest.php');
        $this->assertEquals('php', $output);
    }

    public function testGetFolder()
    {
        $output = Util::getFolder(__DIR__);
        $this->assertArraySubset([], $output);
    }

    public function testArrayPrefixValue()
    {
        $output = Util::arrayPrefixValue([ 'test', 'test1' ], 'prefix');
        $this->assertArraySubset([ 'prefixtest', 'prefixtest1' ], $output);
    }

    public function testInArrayToLower()
    {
        $output = Util::inArrayToLower('Key', [ 'KEY', 'key2', 'KeY3' ]);
        $this->assertTrue($output);
    }

    public function testArrayKeysExists()
    {
        $output = Util::arrayKeysExists([ 'key1', 'key2' ], [
                'key'  => 0,
                'key1' => 1,
                'key2' => 2
        ]);
        $this->assertTrue($output);
    }
}
