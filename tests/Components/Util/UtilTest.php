<?php

namespace Soosyze\Tests\Components\Util;

use Soosyze\Components\Util\Util;

/**
 * @requires extension json
 */
class UtilTest extends \PHPUnit\Framework\TestCase
{
    public static $path = 'tests/Components/Util/build';

    public static $file = 'file';

    public static $pathFile = 'tests/Components/Util/build/file.json';

    public static $pathFileError = 'tests/Components/Util/build/fileError.json';

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
     * @expectedException \Exception
     */
    public function testGetJsonExceptionContent()
    {
        fopen(self::$pathFileError, 'w+');
        Util::getJson(self::$pathFileError);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetJsonExceptionFile()
    {
        Util::getJson('error');
    }

    /**
     * @expectedException \Exception
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

    public function testCleanPath()
    {
        $output = Util::cleanPath('\path/test\file/');
        $this->assertEquals($output, DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'file');
    }

    public function testStrReplaceFirst()
    {
        $output = Util::strReplaceFirst('e', 'a', 'hello');
        $this->assertEquals($output, 'hallo');

        $output2 = Util::strReplaceFirst('z', 'e', 'hello');
        $this->assertEquals($output2, 'hello');
    }

    public function testStrReplaceLast()
    {
        $output = Util::strReplaceLast('l', 'o', 'hello');
        $this->assertEquals($output, 'heloo');

        $output2 = Util::strReplaceLast('z', 'e', 'hello');
        $this->assertEquals($output2, 'hello');
    }

    public function testStrRandom()
    {
        $output = Util::strRandom();
        $this->assertEquals(20, strlen($output));

        $output2 = Util::strRandom(30);
        $this->assertEquals(30, strlen($output2));
    }

    public function testStrSlug()
    {
        $str = 'L\'amBiguïTé PhoNétiQue ';
        $this->assertEquals('l-ambiguite_phonetique', Util::strSlug($str));

        $str = ' StœcHioméTRie';
        $this->assertEquals('stoechiometrie', Util::strSlug($str));
    }
}
