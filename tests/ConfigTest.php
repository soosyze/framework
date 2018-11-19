<?php

namespace Soosyze;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    public static $pathFile = 'tests/config/local/testConfig2.json';

    /**
     * @var Config
     */
    protected $object;

    public static function tearDownAfterClass()
    {
        if (file_exists(self::$pathFile)) {
            unlink(self::$pathFile);
        }
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Config('tests/config', 'local');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testhas()
    {
        $out  = $this->object->has('testConfig.key1');
        $out2 = $this->object->has('testConfig');

        $this->assertTrue($out);
        $this->assertTrue($out2);

        $out3 = $this->object->has('testConfig.notExist');
        $out4 = $this->object->has('notExist');

        $this->assertFalse($out3);
        $this->assertFalse($out4);
    }

    public function testGet()
    {
        $data = $this->object->get('testConfig.key1');

        $this->assertEquals($data, 'value1');
    }

    public function testGetAll()
    {
        $data = $this->object->get('testConfig');

        $this->assertEquals($data, [
            'key1' => 'value1',
            'key2' => 'value2'
        ]);
    }

    public function testGetConfigKeyDefault()
    {
        $this->assertNull($this->object->get('testConfig.error'));
        $this->assertNull($this->object->get('error'));

        $config = $this->object->get('testConfig.error', 'valueDefault');
        $this->assertEquals($config, 'valueDefault');

        $config2 = $this->object->get('error', 'valueDefault');
        $this->assertEquals($config2, 'valueDefault');
    }

    public function testSet()
    {
        $data = $this->object->set('testConfig.key1', 'value1');
        $this->assertEquals($data, true);
    }

    public function testSetNew()
    {
        $data = $this->object->set('testConfig2.key1', 'value1');
        $this->assertEquals($data, true);
    }

    /**
     * @expectedException Exception
     */
    public function testSetException()
    {
        $this->object->set('testConfig', 'value1');
    }

    /**
     * @expectedException Exception
     */
    public function testSetExceptionKey()
    {
        $this->object->set('testConfig.', 'value1');
    }
}
