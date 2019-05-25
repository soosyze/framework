<?php

namespace Soosyze\Test;

use Soosyze\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Config('tests/config', 'local');
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testHasInvalidArgumentException()
    {
        $this->object->has(1);
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
        $this->object->set('testConfig.key3', 'value3');
        $data = $this->object->get('testConfig.key3');
        
        $this->assertEquals($data, 'value3');
    }

    public function testSetNewFile()
    {
        $this->object->set('testConfig2.key1', 'value1');
        $data = $this->object->get('testConfig2.key1');
        
        $this->assertEquals($data, 'value1');
    }
    
    public function testSetFile()
    {
        $this->object->set('testConfig2', 'value1');
        $data = $this->object->get('testConfig2');

        $this->assertEquals($data, [ 'value1' ]);
        
        $this->object->set('testConfig2', ['value2']);
        $data2 = $this->object->get('testConfig2');

        $this->assertEquals($data2, [ 'value2' ]);
    }

    public function testDel()
    {
        $this->object->del('testConfig.key3');
        
        $this->assertNull($this->object->get('testConfig.key3'));
    }

    public function testDelFile()
    {
        $this->object->del('testConfig2');
        
        $this->assertNull($this->object->get('testConfig2.key1'));
    }
    
    public function testDelVoid()
    {
        $this->assertNull($this->object->get('void'));
        $this->object->del('void');
        $this->assertNull($this->object->get('void'));
    }
    
    public function testHasArrayAccess()
    {
        $this->assertTrue(isset($this->object['testConfig.key1']));
    }
    
    public function testGetArrayAccess()
    {
        $data = $this->object['testConfig.key1'];

        $this->assertEquals($data, 'value1');
    }
    
    public function testSetArrayAccess()
    {
        $this->object[ 'testConfig.key3' ] = 'value3';
        $data                            = $this->object[ 'testConfig.key3' ];

        $this->assertEquals($data, 'value3');
    }
    
    public function testDelArrayAccess()
    {
        unset($this->object[ 'testConfig.key3' ]);

        $this->assertNull($this->object[ 'testConfig.key3' ]);
    }
}
