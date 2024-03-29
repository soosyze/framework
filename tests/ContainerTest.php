<?php

namespace Soosyze\Tests;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Soosyze\Container;
use Soosyze\Tests\Resources\Container\Service1;
use Soosyze\Tests\Resources\Container\Service2;
use Soosyze\Tests\Resources\Container\Service3;
use Soosyze\Tests\Resources\Container\Service4;
use Throwable;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Container
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Container;
    }

    public function testSetServices(): void
    {
        $this->object->setServices([
            'service1' => [
                'class' => Service1::class
            ],
            'service2' => [
                'class'     => Service2::class,
                'arguments' => [
                    'service1' => '@service1'
                ]
            ]
        ]);

        $this->assertInstanceOf(Service2::class, $this->object->get('service2'));
    }

    public function testSetService(): void
    {
        $this->object
            ->setService('service2', Service2::class, [
                'arguments' => [ 'service1' => '@service1' ]
            ])
            ->setService('service1', Service1::class);

        $this->assertInstanceOf(Service2::class, $this->object->get('service2'));
    }

    public function testSetServiceParam(): void
    {
        $this->object
            ->setService('service3', Service3::class, [
                'arguments' => [ 'str' => '\@service1' ]
            ])
            ->setService('service1', Service1::class);

        $service3 = $this->object->get('service3');

        $this->assertInstanceOf(Service3::class, $service3);
        $this->assertEquals('@service1', $service3->getStr());
    }

    public function testSetServiceParamConfig(): void
    {
        $this->object
            ->setConfig([ 'testConfig.key1' => 'value1' ])
            ->setService('service3', Service3::class, [
                'arguments' => [ 'str' => '#testConfig.key1' ]
            ])
            ->setService('service1', Service1::class);

        $service3 = $this->object->get('service3');

        $this->assertInstanceOf(Service3::class, $service3);
        $this->assertEquals('value1', $service3->getStr());
    }

    /**
     * @dataProvider providerParamDefaultConfig
     */
    public function testSetServiceParamDefaultConfig(array $args): void
    {
        $this->object
            ->setService('service4', Service4::class, $args)
            ->setService('service1', Service1::class);

        $service3 = $this->object->get('service4');

        $this->assertInstanceOf(Service4::class, $service3);
        $this->assertEquals('default', $service3->getStr());
    }

    public function providerParamDefaultConfig(): \Generator
    {
        yield 'with error config' => [
            [
                'arguments' => [ 'str' => '#testConfig.error' ]
            ]
        ];
        yield 'with void config' => [
            []
        ];
    }

    public function testSetInstance(): void
    {
        $this->object->setInstance('service1', new Service1);

        $service1 = $this->object->get('service1');

        $this->assertInstanceOf(Service1::class, $service1);
        $this->assertTrue($service1->isOk());
    }

    public function testSetInstances(): void
    {
        $service1 = new Service1;
        $service2 = new Service2($service1);

        $this->object->setInstances([ 'service1' => $service1, 'service2' => $service2 ]);

        $getService2 = $this->object->get('service2');

        $this->assertInstanceOf(Service2::class, $getService2);
        $this->assertTrue($getService2->isOk());
    }

    public function testGet(): void
    {
        $this->object->setInstance('service1', new Service1);

        $services = [
            $this->object->get('service1'),
            $this->object->get(Service1::class),
            /** @phpstan-ignore-next-line */
            $this->object->service1()
        ];

        foreach ($services as $service) {
            $this->assertInstanceOf(Service1::class, $service);
            $this->assertTrue($service->isOk());
        }
    }

    /**
     * @dataProvider providerGetException
     *
     * @param class-string<Throwable> $exceptionClass
     */
    public function testGetInvalidArgumentException(
        string $key,
        string $exceptionClass,
        string $exceptionMessage
    ): void {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);
        $this->object->setService('exception', 'Soosyze\Tests');
        $this->object->get($key);
    }

    public function providerGetException(): \Generator
    {
        yield [
            'error',
            NotFoundExceptionInterface::class,
            'Service error does not exist.'
        ];
        yield [
            'exception',
            ContainerExceptionInterface::class,
            'Class exception is not exist.'
        ];
    }

    public function testHas(): void
    {
        $this->object->setInstance('service1', new Service1);

        $this->assertTrue($this->object->has('service1'));
        $this->assertFalse($this->object->has('service9999'));
    }

    public function testHook(): void
    {
        $this->object->addHook('hook.Double', function (&$output) {
            $output *= 2;
        });

        $var = 2;
        $this->object->callHook('hook.double', [ &$var ]);

        $this->assertEquals(4, $var);
    }

    public function testHookForService(): void
    {
        $this->object->setServices([
            'service1' => [
                'class' => Service1::class,
                'hooks' => [ 'hook.double' => 'hookDouble' ]
            ]
        ]);

        $var = 2;
        $this->object->callHook('hook.Double', [ &$var ]);

        $this->assertEquals(4, $var);
    }

    public function testSetConfigInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->object->setConfig('error');
    }

    public function testCalls(): void
    {
        $this->object
            ->setService('service1', Service1::class, [
                'calls' => [ 'setData' => 1 ]
        ]);

        $service1 = $this->object->get('service1');

        $this->assertInstanceOf(Service1::class, $service1);
        $this->assertEquals(1, $service1->getData());
    }
}
