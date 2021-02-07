<?php

namespace Soosyze\Tests;

use Soosyze\Autoload;

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
        $this->object = new Autoload([
            'Soosyze\Tests'            => __DIR__,
            'Soosyze\Tests\Components' => __DIR__ . '\Components'
        ]);

        $this->object->register();
    }

    public function testSetPrefix()
    {
        $this->object->setPrefix([]);
        $this->assertAttributeSame([], 'prefix', $this->object);
    }

    public function testPrefix()
    {
        $this->object->setPrefix([ 'Soosyze\Tests' => __DIR__ ]);
        $class = $this->object->loader('Soosyze\Tests\AppTest');

        $file = __DIR__ . '\AppTest.php';
        $this->assertEquals($class, str_replace('\\', DIRECTORY_SEPARATOR, $file));
    }

    public function testAutoloadPrefixError()
    {
        $this->object->setPrefix([ 'Soosyze\Tests' => __DIR__ ]);

        $class = $this->object->loader('Soosyze\Tests\Error');
        $this->assertFalse($class);
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
        $class = $this->object->loader('Soosyze\Tests\AppTest');
        $file  = __DIR__ . '\AppTest.php';
        $this->assertEquals($class, str_replace('\\', DIRECTORY_SEPARATOR, $file));

        $class = $this->object->loader('Soosyze\Tests\Components\Http\MessageTest');
        $file  = __DIR__ . '\Components\Http\MessageTest.php';
        $this->assertEquals($class, str_replace('\\', DIRECTORY_SEPARATOR, $file));
    }

    public function testAutoloadLibError()
    {
        $class = $this->object->loader('Soosyze\Tests\Components\Http\Error');
        $this->assertFalse($class);
    }

    public function testAutoloadMap()
    {
        $auto = new Autoload;
        $auto->setMap([
            __DIR__
        ]);

        $class = $auto->loader('Components\Http\MessageTest');
        $file  = __DIR__ . '\Components\Http\MessageTest.php';
        $this->assertEquals($class, str_replace('\\', DIRECTORY_SEPARATOR, $file));
    }

    public function testAutoloadMapError()
    {
        $auto = new Autoload;
        $auto->setMap([
            __DIR__ . '\error'
        ]);

        $class = $auto->loader('Soosyze\Tests\Components\Http\MessageTest');
        $this->assertFalse($class);
    }
}
