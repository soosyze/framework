<?php

namespace Soosyze\Tests;

use Soosyze\Container;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Container
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Soosyze\Container;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testSetServices()
    {
        $this->object->SetServices([
            'service1' => [
                'class' => 'Soosyze\Tests\service1'
            ],
            'service2' => [
                'class'     => 'Soosyze\Tests\service2',
                'arguments' => [
                    '@service1'
                ]
            ]
        ]);

        $isOk = $this->object->get('service2')->isOk();

        $this->assertTrue($isOk);
    }

    public function testSetService()
    {
        $this->object
            ->SetService('service2', 'Soosyze\Tests\service2', [ '@service1' ])
            ->SetService('service1', 'Soosyze\Tests\service1');

        $isOk = $this->object->get('service2')->isOk();

        $this->assertTrue($isOk);
    }

    public function testSetServiceParam()
    {
        $this->object
            ->SetService('service3', 'Soosyze\Tests\service3', [ '@service1', '\@service1' ])
            ->SetService('service1', 'Soosyze\Tests\service1');

        $str = $this->object->get('service3')->getStr();

        $this->assertEquals($str, '@service1');
    }

    public function testSetServiceParamConfig()
    {
        $this->object
            ->setConfig([ 'testConfig.key1' => 'value1' ])
            ->SetService('service3', 'Soosyze\Tests\service3', [ '@service1', '#testConfig.key1' ])
            ->SetService('service1', 'Soosyze\Tests\service1');

        $str = $this->object->get('service3')->getStr();

        $this->assertEquals($str, 'value1');
    }

    public function testSetInstance()
    {
        $this->object->setInstance('service1', new Service1);

        $isOk = $this->object->get('service1')->isOk();

        $this->assertTrue($isOk);
    }

    public function testSetInstances()
    {
        $service1 = new Service1;
        $service2 = new Service2($service1, '');

        $this->object->setInstances([ 'service1' => $service1, 'service2' => $service2 ]);

        $isOk = $this->object->get('service2')->isOk();

        $this->assertTrue($isOk);
    }

    public function testGet()
    {
        $this->object->setInstance('service1', new Service1);

        $isOk = $this->object->get('service1')->isOk();
        $this->assertTrue($isOk);
        $isOk = $this->object->service1()->isOk();
        $this->assertTrue($isOk);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetInvalidArgumentException()
    {
        $this->object->get(1);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetNotFoundException()
    {
        $this->object->get('error');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetContainerException()
    {
        $this->object->SetService('service', 'Soosyze\Tests')->get('service');
    }

    public function testHas()
    {
        $this->object->setInstance('service1', new Service1);

        $this->assertTrue($this->object->has('service1'));
    }

    /**
     * @expectedException \Exception
     */
    public function testHasException()
    {
        $this->object->has(1);
    }

    public function testHook()
    {
        $this->object->addHook('hook.Double', function (&$output) {
            $output *= 2;
        });

        $var = 2;
        $this->object->callHook('hook.double', [ &$var ]);

        $this->assertEquals(4, $var);
    }

    public function testHookForService()
    {
        $this->object->SetServices([
            'service1' => [
                'class' => 'Soosyze\Tests\service1',
                'hooks' => [ 'hook.double' => 'hookDouble' ]
            ]
        ]);

        $var = 2;
        $this->object->callHook('hook.Double', [ &$var ]);

        $this->assertEquals(4, $var);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetConfigInvalidArgumentException()
    {
        $this->object->setConfig('error');
    }
}

class service1
{
    public function isOk()
    {
        return true;
    }

    public function hookDouble(&$var)
    {
        $var *= 2;
    }
}

class service2
{
    protected $service;

    public function __construct(Service1 $arg1)
    {
        $this->service = $arg1;
    }

    public function isOk()
    {
        return $this->service->isOk();
    }
}

class service3
{
    protected $service;

    protected $str;

    public function __construct(Service1 $arg1, $arg2)
    {
        $this->service = $arg1;
        $this->str     = $arg2;
    }

    public function getStr()
    {
        return $this->str;
    }
}
