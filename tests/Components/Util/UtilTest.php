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
        Util::getJson(__FILE__);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetJsonExceptionGetContents()
    {
        Util::getJson(fopen('php://temp', '+r'));
    }

    public function testGetJson()
    {
        $output = Util::getJson(self::$pathFile);
        $this->assertArraySubset([ 'key' => 'value' ], $output);
    }

    public function testGetFileExtension()
    {
        $output = Util::getFileExtension(__FILE__);
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
        $output = Util::cleanPath('\path//test\\file/');
        $this->assertEquals($output, '/path/test/file');
    }

    public function testcleanDir()
    {
        $output = Util::cleanDir('\path//test\\file/');
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
        $str1 = '-_L\'amBiguïTé PhoNétiQue- ';
        $this->assertEquals('l_ambiguite_phonetique', Util::strSlug($str1));
        
        $str2 = '-_L\'amBiguïTé PhoNétiQue- ';
        $this->assertEquals('l-ambiguite-phonetique', Util::strSlug($str2, '-'));

        $str3 = ' StœcHioméTRie-cHiMIE';
        $this->assertEquals('stoechiometrie_chimie', Util::strSlug($str3));
    }

    public function testStrFileSizeFormatted()
    {
        $this->assertEquals('', Util::strFileSizeFormatted(0));
        $this->assertEquals('1 Kb', Util::strFileSizeFormatted(1024));
        $this->assertEquals('1 023 b', Util::strFileSizeFormatted(1023));
        $this->assertEquals('2.43 Kb', Util::strFileSizeFormatted(2487));
        $this->assertEquals('2.429 Kb', Util::strFileSizeFormatted(2487, 3));
    }
    
    public function testGetOctetShortBytesPhp()
    {
        $this->assertEquals(0, Util::getOctetShortBytesPhp(0));
        $this->assertEquals(1, Util::getOctetShortBytesPhp('1 k b'));
        $this->assertEquals(1024, Util::getOctetShortBytesPhp('1 m k'));
        $this->assertEquals(1024, Util::getOctetShortBytesPhp('1 rqgJdsg5k'));
        $this->assertEquals(1024, Util::getOctetShortBytesPhp('1 kk'));
        $this->assertEquals(1024, Util::getOctetShortBytesPhp(1024));
        $this->assertEquals(1024, Util::getOctetShortBytesPhp('1K'));
        $this->assertEquals(1048576, Util::getOctetShortBytesPhp('1M'));
        $this->assertEquals(1073741824, Util::getOctetShortBytesPhp('1G'));
        $this->assertEquals(null, Util::getOctetShortBytesPhp('G'));
        $this->assertEquals(null, Util::getOctetShortBytesPhp('G1'));
        $this->assertEquals(null, Util::getOctetShortBytesPhp('G1k'));
        $this->assertEquals(null, Util::getOctetShortBytesPhp('-1'));
        $this->assertEquals(null, Util::getOctetShortBytesPhp(-1));
        $this->assertEquals(1, Util::getOctetShortBytesPhp(1.5));
        $this->assertEquals(15, Util::getOctetShortBytesPhp(15.875));
    }
    
    public function testStrTimeDiffHumans()
    {
        /* YEARS */
        $year   = Util::strHumansTimeDiff(date_create('now +1 year +1second'));
        $this->assertEquals('1 year', sprintf($year[ 0 ], $year[ 1 ]));
        $year   = Util::strHumansTimeDiff(date_create('now -1 year'));
        $this->assertEquals('1 year ago', sprintf($year[ 0 ], $year[ 1 ]));
        $year   = Util::strHumansTimeDiff(date_create('now +2 year +1 second'));
        $this->assertEquals('2 years', sprintf($year[ 0 ], $year[ 1 ]));
        $year   = Util::strHumansTimeDiff(date_create('now -2 year'));
        $this->assertEquals('2 years ago', sprintf($year[ 0 ], $year[ 1 ]));
        
        /* MONTH */
        $month  = Util::strHumansTimeDiff(date_create('now +1 month +1 second'));
        $this->assertEquals('1 month', sprintf($month[ 0 ], $month[ 1 ]));
        $month  = Util::strHumansTimeDiff(date_create('now -1 month'));
        $this->assertEquals('1 month ago', sprintf($month[ 0 ], $month[ 1 ]));
        $month  = Util::strHumansTimeDiff(date_create('now +2 month +1 second'));
        $this->assertEquals('2 months', sprintf($month[ 0 ], $month[ 1 ]));
        $month  = Util::strHumansTimeDiff(date_create('now -2 month'));
        $this->assertEquals('2 months ago', sprintf($month[ 0 ], $month[ 1 ]));
        
        /* WEEK */
        $week   = Util::strHumansTimeDiff(date_create('now +1 week +1 second'));
        $this->assertEquals('1 week', sprintf($week[ 0 ], $week[ 1 ]));
        $week   = Util::strHumansTimeDiff(date_create('now -1 week'));
        $this->assertEquals('1 week ago', sprintf($week[ 0 ], $week[ 1 ]));
        $week   = Util::strHumansTimeDiff(date_create('now +2 week +1 second'));
        $this->assertEquals('2 weeks', sprintf($week[ 0 ], $week[ 1 ]));
        $week   = Util::strHumansTimeDiff(date_create('now -2 week'));
        $this->assertEquals('2 weeks ago', sprintf($week[ 0 ], $week[ 1 ]));
        
        /* DAY */
        $day    = Util::strHumansTimeDiff(date_create('now +1 day +1 second'));
        $this->assertEquals('1 day', sprintf($day[ 0 ], $day[ 1 ]));
        $day    = Util::strHumansTimeDiff(date_create('now -1 day'));
        $this->assertEquals('1 day ago', sprintf($day[ 0 ], $day[ 1 ]));
        $day    = Util::strHumansTimeDiff(date_create('now +2 day +1 second'));
        $this->assertEquals('2 days', sprintf($day[ 0 ], $day[ 1 ]));
        $day    = Util::strHumansTimeDiff(date_create('now -2 day'));
        $this->assertEquals('2 days ago', sprintf($day[ 0 ], $day[ 1 ]));
        
        /* HOUR */
        $hour   = Util::strHumansTimeDiff(date_create('now +1 hour +1 second'));
        $this->assertEquals('1 hour', sprintf($hour[ 0 ], $hour[ 1 ]));
        $hour   = Util::strHumansTimeDiff(date_create('now -1 hour'));
        $this->assertEquals('1 hour ago', sprintf($hour[ 0 ], $hour[ 1 ]));
        $hour   = Util::strHumansTimeDiff(date_create('now +2 hour +1 second'));
        $this->assertEquals('2 hours', sprintf($hour[ 0 ], $hour[ 1 ]));
        $hour   = Util::strHumansTimeDiff(date_create('now -2 hour'));
        $this->assertEquals('2 hours ago', sprintf($hour[ 0 ], $hour[ 1 ]));
        
        /* MINUTE */
        $minute = Util::strHumansTimeDiff(date_create('now +1 minute +1 second'));
        $this->assertEquals('1 minute', sprintf($minute[ 0 ], $minute[ 1 ]));
        $minute = Util::strHumansTimeDiff(date_create('now -1 minute'));
        $this->assertEquals('1 minute ago', sprintf($minute[ 0 ], $minute[ 1 ]));
        $minute = Util::strHumansTimeDiff(date_create('now +2 minute +1 second'));
        $this->assertEquals('2 minutes', sprintf($minute[ 0 ], $minute[ 1 ]));
        $minute = Util::strHumansTimeDiff(date_create('now -2 minute'));
        $this->assertEquals('2 minutes ago', sprintf($minute[ 0 ], $minute[ 1 ]));
    }
}
