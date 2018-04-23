<?php

namespace Soosyze\Test;

class ContainerTest extends \PHPUnit_Framework_TestCase
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
                "class" => "Soosyze\\Test\\service1"
            ],
            'service2' => [
                "class"     => "Soosyze\\Test\\service2",
                "arguments" => [
                    "@service1",
                ]
            ]
        ]);

        $isOk = $this->object->get('service2')->isOk();

        $this->assertTrue($isOk);
    }

    public function testSetService()
    {
        $this->object->SetService('service2', "Soosyze\\Test\\service2", [ '@service1' ]);
        $this->object->SetService('service1', "Soosyze\\Test\\service1");

        $isOk = $this->object->get('service2')->isOk();

        $this->assertTrue($isOk);
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
        $service2 = new Service2($service1);

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
     * @expectedException Exception
     */
    public function testGetInvalidArgumentException()
    {
        $this->object->get(1);
    }

    /**
     * @expectedException Exception
     */
    public function testGetNotFoundException()
    {
        $this->object->get('error');
    }

    /**
     * @expectedException Exception
     */
    public function testGetContainerException()
    {
        $this->object->SetService('service', "Soosyze\\Test")->get('service');
    }

    public function testHas()
    {
        $this->object->setInstance('service1', new Service1);

        $this->assertTrue($this->object->has('service1'));
    }

    /**
     * @expectedException Exception
     */
    public function testHasException()
    {
        $this->object->has(1);
    }

    public function testHook()
    {
        $this->object->addHook('hook.double', function(&$output)
        {
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
                "class" => "Soosyze\\Test\\service1",
                "hooks" => [ "hook.double" => "hookDouble" ]
            ]
        ]);

        $var = 2;
        $this->object->callHook('hook.double', [ &$var ]);

        $this->assertEquals(4, $var);
    }
}

class service1
{

    public function isOk()
    {
        return true;
    }

    public function hookDouble( &$var )
    {
        $var *= 2;
    }
}

class service2
{
    protected $service;

    public function __construct( Service1 $service1 )
    {
        $this->service = $service1;
    }

    public function isOk()
    {
        return $this->service->isOk();
    }
}