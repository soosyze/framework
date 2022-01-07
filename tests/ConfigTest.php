<?php

namespace Soosyze\Tests;

use Soosyze\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Config(__DIR__ . '/Resources/App/config', 'local');
    }

    public function testhas(): void
    {
        $this->assertTrue($this->object->has('testConfig.key1'));
        $this->assertTrue($this->object->has('testConfig'));
        $this->assertFalse($this->object->has('testConfig.notExist'));
        $this->assertFalse($this->object->has('notExist'));
    }

    /**
     * @dataProvider providerConfigGet
     *
     * @param array|string $expectedValue
     */
    public function testGet($expectedValue, string $config): void
    {
        $this->assertEquals($expectedValue, $this->object->get($config));
    }

    public function providerConfigGet(): \Generator
    {
        yield [ 'value1', 'testConfig.key1' ];
        yield [ [ 'key1' => 'value1', 'key2' => 'value2' ], 'testConfig' ];
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
        yield [ 'testConfig.error', 'valueDefault' ];
        yield [ 'error', 'valueDefault' ];
    }

    /**
     * @dataProvider providerSetConfig
     *
     * @param array|string $value
     * @param array|string $expectedValue
     */
    public function testSet(string $config, $value, $expectedValue): void
    {
        $this->object->set($config, $value);

        $this->assertEquals($expectedValue, $this->object->get($config));
    }

    public function providerSetConfig(): \Generator
    {
        /* Par clé */
        yield [ 'testConfig.key3', 'value3', 'value3' ];
        yield [ 'testConfig2.key1', 'value1', 'value1' ];
        /* Par fichiers */
        yield [ 'testConfig2', 'value1', [ 'value1' ] ];
        yield [ 'testConfig2', [ 'value1', 'value2' ], [ 'value1', 'value2' ] ];
    }

    public function testDel(): void
    {
        $this->object->del('testConfig.key3');

        $this->assertNull($this->object->get('testConfig.key3'));
    }

    public function testDelFile(): void
    {
        $this->object->del('testConfig2');

        $this->assertNull($this->object->get('testConfig2.key1'));
    }

    public function testDelVoid(): void
    {
        $this->assertNull($this->object->get('void'));
        $this->object->del('void');
        $this->assertNull($this->object->get('void'));
    }

    public function testHasArrayAccess(): void
    {
        $this->assertTrue(isset($this->object[ 'testConfig.key1' ]));
    }

    public function testHasArrayAccessInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The key of must be of type string: integer given');
        /** @phpstan-ignore-next-line */
        isset($this->object[ 1 ]);
    }

    public function testGetArrayAccess(): void
    {
        $this->assertEquals('value1', $this->object[ 'testConfig.key1' ]);
    }

    public function testGetArrayAccessInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The key of must be of type string: integer given');
        /** @phpstan-ignore-next-line */
        $this->object[ 1 ];
    }

    public function testSetArrayAccess(): void
    {
        $this->object[ 'testConfig.key3' ] = 'value3';

        $this->assertEquals('value3', $this->object[ 'testConfig.key3' ]);
    }

    public function testSetArrayAccessInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The key of must be of type string: integer given');
        $this->object[ 1 ] = 'value3';
    }

    public function testDelArrayAccess(): void
    {
        unset($this->object[ 'testConfig.key3' ]);

        $this->assertNull($this->object[ 'testConfig.key3' ]);
    }

    public function testDelArrayAccessInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The key of must be of type string: integer given');
        unset($this->object[ 1 ]);
    }
}
