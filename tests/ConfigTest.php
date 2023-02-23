<?php

namespace Soosyze\Tests;

use Soosyze\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    private const PATH = 'tests/Resources/Config';

    private const PATH_FILE_GET = self::PATH . '/data.json';

    private const PATH_FILE_SET = self::PATH . '/dataSet.json';

    private const PATH_FILE_DELETE = self::PATH . '/dataDelete.json';

    /**
     * @var Config
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Config(__DIR__ . '/Resources/Config');
    }

    protected function tearDown(): void
    {
        if (file_exists(self::PATH_FILE_SET)) {
            unlink(self::PATH_FILE_SET);
        }
        if (file_exists(self::PATH_FILE_DELETE)) {
            unlink(self::PATH_FILE_DELETE);
        }
    }

    public function testhas(): void
    {
        $this->assertTrue($this->object->has('data'));
        $this->assertTrue($this->object->has('data.key'));
        $this->assertTrue($this->object->has('data.object1.key1'));
        $this->assertTrue($this->object->has('data.object1.object2.key2'));

        $this->assertFalse($this->object->has('notExist'));
        $this->assertFalse($this->object->has('data.notExist'));
    }

    /**
     * @dataProvider providerConfigGet
     *
     * @param array|string $expectedValue
     */
    public function testGet(string $config, $expectedValue): void
    {
        $this->assertEquals($expectedValue, $this->object->get($config));
    }

    public function providerConfigGet(): \Generator
    {
        yield 'without' => [
            'data',
            [
                'key' => 'value',
                'object1' => [
                    'key1' => 'value1',
                    'object2' => ['key2' => 'value2']
                ]
            ]
        ];
        yield 'with key' => [
            'data.key', 'value',
        ];
        yield 'with key that returns object' => [
            'data.object1',
            [
                'key1' => 'value1',
                'object2' => ['key2' => 'value2']
            ],
        ];
        yield 'with 2 keys' => [
            'data.object1.key1', 'value1',
        ];
        yield 'with 3 keys' => [
            'data.object1.object2.key2', 'value2',
        ];
    }

    /**
     * @dataProvider providerConfigKeyDefault
     */
    public function testGetConfigKeyDefault(
        string $config,
        string $valueDefault
    ): void {
        $this->assertNull($this->object->get($config));
        $this->assertEquals($valueDefault, $this->object->get($config, $valueDefault));
    }

    public function providerConfigKeyDefault(): \Generator
    {
        yield ['error', 'valueDefault'];
        yield ['error.key1', 'valueDefault'];
        yield ['error.key1.key2', 'valueDefault'];
    }

    /**
     * @dataProvider providerSetConfig
     *
     * @param array|string $value
     * @param array|string $expectedValue
     */
    public function testSet(string $config, $value, $expectedValue): void
    {
        //copy(self::PATH_FILE_GET, self::PATH_FILE_SET);

        $this->object->set($config, $value);

        $this->assertEquals($expectedValue, $this->object->get($config));
    }

    public function providerSetConfig(): \Generator
    {
        /* Par clé */
        yield ['dataSet.key1', 'value1', 'value1'];
        yield ['dataSet.object2', ['value2'], ['value2']];
        /* Par fichiers */
        yield ['dataSet', 'value1', ['value1']];
        yield ['dataSet', ['value1', 'value2'], ['value1', 'value2']];
    }

    public function testDel(): void
    {
        copy(self::PATH_FILE_GET, self::PATH_FILE_DELETE);

        $this->object->del('dataDelete.key');

        $this->assertNull($this->object->get('dataDelete.key'));
    }

    public function testDelFile(): void
    {
        copy(self::PATH_FILE_GET, self::PATH_FILE_DELETE);

        $this->object->del('dataDelete');

        $this->assertNull($this->object->get('dataDelete'));
        $this->assertFileNotExists(self::PATH_FILE_DELETE);
    }

    public function testDelVoid(): void
    {
        $this->assertNull($this->object->get('void'));
        $this->object->del('void');
        $this->assertNull($this->object->get('void'));
    }

    public function testOffsetExists(): void
    {
        $this->assertTrue(isset($this->object['data.key']));
    }

    public function testOffsetExistsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The key of must be of type string: integer given');
        $this->assertIsBool(isset($this->object[1]));
    }

    public function testOffsetGet(): void
    {
        $this->assertEquals('value', $this->object['data.key']);
    }

    public function testOffsetGetInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The key of must be of type string: integer given');
        /** @phpstan-ignore-next-line */
        $this->object[1];
    }

    public function testOffsetSet(): void
    {
        copy(self::PATH_FILE_GET, self::PATH_FILE_SET);

        $this->object['dataSet.key3'] = 'value3';

        $this->assertEquals('value3', $this->object['dataSet.key3']);
    }

    public function testOffsetSetInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The key of must be of type string: integer given');
        $this->object[1] = 'value3';
    }

    public function testOffsetUnset(): void
    {
        copy(self::PATH_FILE_GET, self::PATH_FILE_DELETE);

        unset($this->object['dataDelete.key']);

        $this->assertNull($this->object['dataDelete.key']);
    }

    public function testOffsetUnsetInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The key of must be of type string: integer given');
        unset($this->object[1]);
    }
}
