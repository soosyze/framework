<?php

namespace Soosyze\Test;

class AutoloadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Soosyze\Autoload
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Soosyze\Autoload([
            'Soosyze\Tests'            => __DIR__,
            'Soosyze\Tests\Components' => __DIR__ . '\Components'
        ]);

        $this->object->register();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    public function testSetLib()
    {
        $this->object->setLib([]);
        $this->assertAttributeSame([], 'lib', $this->object);
    }

    public function testLib()
    {
        $this->assertAttributeSame([
            'Soosyze\Tests'            => __DIR__,
            'Soosyze\Tests\Components' => __DIR__ . '\Components'
            ], 'lib', $this->object);
    }

    public function testAutoloadLib()
    {
        $class = $this->object->autoload('Soosyze\Tests\AppTest');
        $file  = __DIR__ . '\AppTest.php';
        $this->assertEquals($class, str_replace('\\', '/', $file));

        $class = $this->object->autoload('Soosyze\Tests\Components\Http\MessageTest');
        $file  = __DIR__ . '\Components\Http\MessageTest.php';
        $this->assertEquals($class, str_replace('\\', '/', $file));
    }

    public function testAutoloadLibError()
    {
        $class = $this->object->autoload('Soosyze\Tests\Components\Http\Error');
        $this->assertFalse($class);
    }

    public function testAutoloadMap()
    {
        $auto = new \Soosyze\Autoload;
        $auto->setMap([
            __DIR__
        ]);

        $class = $auto->autoload('Components\Http\MessageTest');
        $file  = __DIR__ . '\Components\Http\MessageTest.php';
        $this->assertEquals($class, str_replace('\\', '/', $file));
    }

    public function testAutoloadMapError()
    {
        $auto = new \Soosyze\Autoload;
        $auto->setMap([
            __DIR__ . '\error'
        ]);

        $class = $auto->autoload('Soosyze\Tests\Components\Http\MessageTest');
        $this->assertFalse($class);
    }
}