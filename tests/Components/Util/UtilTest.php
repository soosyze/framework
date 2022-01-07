<?php

namespace Soosyze\Tests\Components\Util;

use Soosyze\Components\Util\Util;
use Soosyze\Tests\Traits\DateTime;

require_once __DIR__ . '/../../Resources/Functions.php';

/**
 * @requires extension json
 */
class UtilTest extends \PHPUnit\Framework\TestCase
{
    use DateTime;

    private const PATH = 'tests/Components/Util/build';

    private const FILE = 'file';

    private const PATH_FILE = 'tests/Components/Util/build/file.json';

    private const PATH_FILE_ERROR = 'tests/Components/Util/build/fileError.json';

    public static function tearDownAfterClass(): void
    {
        if (file_exists(self::PATH_FILE)) {
            unlink(self::PATH_FILE);
        }
        if (file_exists(self::PATH_FILE_ERROR)) {
            unlink(self::PATH_FILE_ERROR);
        }
        if (is_dir(self::PATH)) {
            rmdir(self::PATH);
        }
    }

    public function testCreateJson(): void
    {
        $output = Util::createJson(self::PATH, self::FILE);
        $this->assertFileExists(self::PATH_FILE);
        $this->assertTrue($output);

        $output = Util::createJson(self::PATH, self::FILE);
        $this->assertNull($output);
    }

    public function testSaveJson(): void
    {
        $output = Util::saveJson(self::PATH, self::FILE, [ 'key' => 'value' ]);
        $this->assertTrue($output);
    }

    public function testGetJsonExceptionContent(): void
    {
        fopen(self::PATH_FILE_ERROR, 'w+');

        $this->expectException(\Exception::class);
        Util::getJson(self::PATH_FILE_ERROR);
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
        $this->assertEquals([ 'key' => 'value' ], Util::getJson(self::PATH_FILE));
    }

    public function testGetFileExtension(): void
    {
        $this->assertEquals('php', Util::getFileExtension(__FILE__));
    }

    public function testGetFolder(): void
    {
        $this->assertEquals([ 'build' ], Util::getFolder(__DIR__));
    }

    public function testArrayPrefixValue(): void
    {
        $this->assertEquals(
            [ 'prefixtest', 'prefixtest1' ],
            Util::arrayPrefixValue([ 'test', 'test1' ], 'prefix')
        );
    }

    public function testInArrayToLower(): void
    {
        $this->assertTrue(Util::inArrayToLower('Key', [ 'KEY', 'key2', 'KeY3' ]));
    }

    public function testArrayKeysExists(): void
    {
        $output = Util::arrayKeysExists([ 'key1', 'key2' ], [
                'key'  => 0,
                'key1' => 1,
                'key2' => 2
        ]);
        $this->assertTrue($output);
    }

    public function testCleanPath(): void
    {
        $this->assertEquals('/path/test/file', Util::cleanPath('\path//test\\file/'));
    }

    public function testcleanDir(): void
    {
        $output = Util::cleanDir('\path//test\\file/');
        $this->assertEquals($output, DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'file');
    }

    public function testStrReplaceFirst(): void
    {
        $output = Util::strReplaceFirst('e', 'a', 'hello');
        $this->assertEquals('hallo', $output);

        $output2 = Util::strReplaceFirst('z', 'e', 'hello');
        $this->assertEquals('hello', $output2);
    }

    public function testStrReplaceLast(): void
    {
        $output = Util::strReplaceLast('l', 'o', 'hello');
        $this->assertEquals('heloo', $output);

        $output2 = Util::strReplaceLast('z', 'e', 'hello');
        $this->assertEquals('hello', $output2);
    }

    public function testStrRandom(): void
    {
        $output = Util::strRandom();
        $this->assertEquals(20, strlen($output));

        $output2 = Util::strRandom(30);
        $this->assertEquals(30, strlen($output2));
    }

    public function testStrHighlight(): void
    {
        $needle   = 'hello';
        $haystack = 'hello wolrd';

        $this->assertEquals(
            'hello wolrd',
            Util::strHighlight('', $haystack)
        );

        $this->assertEquals(
            '<span class="highlight">hello</span> wolrd',
            Util::strHighlight($needle, $haystack)
        );

        $this->assertEquals(
            '<span class="foo">hello</span> wolrd',
            Util::strHighlight($needle, $haystack, 'foo')
        );
    }

    public function testStrSlug(): void
    {
        $str1 = '-_L\'amBiguïTé PhoNétiQue- ';
        $this->assertEquals('l_ambiguite_phonetique', Util::strSlug($str1));

        $str2 = '-_L\'amBiguïTé PhoNétiQue- ';
        $this->assertEquals('l-ambiguite-phonetique', Util::strSlug($str2, '-'));

        $str3 = ' StœcHioméTRie-cHiMIE';
        $this->assertEquals('stoechiometrie_chimie', Util::strSlug($str3));
    }

    public function testStrFileSizeFormatted(): void
    {
        $this->assertEquals('', Util::strFileSizeFormatted(0));
        $this->assertEquals('1 Kb', Util::strFileSizeFormatted(1024));
        $this->assertEquals('1 023 b', Util::strFileSizeFormatted(1023));
        $this->assertEquals('2.43 Kb', Util::strFileSizeFormatted(2487));
        $this->assertEquals('2.429 Kb', Util::strFileSizeFormatted(2487, 3));
    }

    /**
     * @dataProvider providerGetOctetShortBytesPhp
     */
    public function testGetOctetShortBytesPhp(?int $expectedOctet, string $octet): void
    {
        $this->assertEquals($expectedOctet, Util::getOctetShortBytesPhp($octet));
    }

    public function providerGetOctetShortBytesPhp(): \Generator
    {
        yield [0, '0'];
        yield [1, '1 k b'];
        yield [1024, '1 m k'];
        yield [1024, '1 rqgJdsg5k'];
        yield [1024, '1 kk'];
        yield [1024, '1024'];
        yield [1024, '1K'];
        yield [1048576, '1M'];
        yield [1073741824, '1G'];
        yield [null, 'G'];
        yield [null, 'G1'];
        yield [null, 'G1k'];
        yield [null, '-1'];
        yield [null, '-1'];
        yield [1, '1.5'];
        yield [ 15, '15.875' ];
    }

    public function testGetOctetUploadLimit(): void
    {
        \Soosyze\Components\Util\Input::reset();
        $this->assertEquals(1024, Util::getOctetUploadLimit());

        \Soosyze\Components\Util\Input::addIni('upload_max_filesize', null);
        \Soosyze\Components\Util\Input::addIni('post_max_size', null);
        \Soosyze\Components\Util\Input::addIni('memory_limit', null);
        $this->assertNull(Util::getOctetUploadLimit());
    }

    /**
     * @dataProvider providerStrTimeDiffHumans
     */
    public function testStrTimeDiffHumans(string $expectedHumansTime, string $date): void
    {
        $data = Util::strHumansTimeDiff(self::dateCreate($date));
        $this->assertEquals($expectedHumansTime, sprintf($data[ 0 ], $data[ 1 ]));
    }

    public function providerStrTimeDiffHumans(): \Generator
    {
        /* YEARS */
        yield ['1 year', 'now +1 year +1 second'];
        yield ['1 year ago', 'now -1 year'];
        yield ['2 years', 'now +2 year +1 second'];
        yield ['2 years ago', 'now -2 years'];
        /* MONTH */
        yield ['1 month', 'now +1 month +1 second'];
        yield ['1 month ago', 'now -1 month'];
        yield ['2 months', 'now +2 month +1 second'];
        yield ['2 months ago', 'now -2 month'];
        /* WEEK */
        yield ['1 week', 'now +1 week +1 second'];
        yield ['1 week ago', 'now -1 week'];
        yield ['2 weeks', 'now +2 week +1 second'];
        yield ['2 weeks ago', 'now -2 week'];
        /* DAY */
        yield ['1 day', 'now +1 day +1 second'];
        yield ['1 day ago', 'now -1 day'];
        yield ['2 days', 'now +2 day +1 second'];
        yield ['2 days ago', 'now -2 day'];
        /* HOUR */
        yield ['1 hour', 'now +1 hour +1 second'];
        yield ['1 hour ago', 'now -1 hour'];
        yield ['2 hours', 'now +2 hour +1 second'];
        yield ['2 hours ago', 'now -2 hour'];
        /* MINUTE */
        yield ['1 minute', 'now +1 minute +1 second'];
        yield ['1 minute ago', 'now -1 minute'];
        yield ['2 minutes', 'now +2 minute +1 second'];
        yield ['2 minutes ago', 'now -2 minute'];
    }

    public function testTryFopenRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to open "fileError.json" using mode "d".');
        Util::tryFopen('fileError.json', 'd');
    }

    public function testTryDateCreateRuntimeException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The date must be in valid format.');
        Util::tryDateCreate('error');
    }
}
