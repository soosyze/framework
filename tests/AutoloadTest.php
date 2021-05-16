<?php

namespace Soosyze\Tests;

use Soosyze\Autoload;

class AutoloadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Soosyze\Autoload
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Autoload([
            'Soosyze\Tests'            => __DIR__,
            'Soosyze\Tests\Components' => __DIR__ . '\Components'
        ]);

        $this->object->register();
    }

    public function testPrefix(): void
    {
        $this->object->setPrefix([ 'Soosyze\Tests' => __DIR__ ]);
        $class = $this->object->loader('Soosyze\Tests\AppTest');

        $expectedClass = str_replace('\\', DIRECTORY_SEPARATOR, __DIR__ . '\AppTest.php');
        $this->assertEquals($expectedClass, $class);
    }

    public function testAutoloadPrefixError(): void
    {
        $this->object->setPrefix([ 'Soosyze\Tests' => __DIR__ ]);

        $class = $this->object->loader('Soosyze\Tests\Error');
        $this->assertNull($class);
    }

    public function testAutoloadLib(): void
    {
        $class         = $this->object->loader('Soosyze\Tests\AppTest');
        $expectedClass = str_replace('\\', DIRECTORY_SEPARATOR, __DIR__ . '\AppTest.php');
        $this->assertEquals($expectedClass, $class);

        $class         = $this->object->loader('Soosyze\Tests\Components\Http\MessageTest');
        $expectedClass = str_replace('\\', DIRECTORY_SEPARATOR, __DIR__ . '\Components\Http\MessageTest.php');
        $this->assertEquals($expectedClass, $class);
    }

    public function testAutoloadLibError(): void
    {
        $class = $this->object->loader('Soosyze\Tests\Components\Http\Error');
        $this->assertNull($class);
    }

    public function testAutoloadMap(): void
    {
        $auto = new Autoload;
        $auto->setMap([
            __DIR__
        ]);

        $class = $auto->loader('Components\Http\MessageTest');
        $expectedClass  = str_replace('\\', DIRECTORY_SEPARATOR, __DIR__ . '\Components\Http\MessageTest.php');
        $this->assertEquals($expectedClass, $class);
    }

    public function testAutoloadMapError(): void
    {
        $auto = new Autoload;
        $auto->setMap([
            __DIR__ . '\error'
        ]);

        $class = $auto->loader('Soosyze\Tests\Components\Http\MessageTest');
        $this->assertNull($class);
    }
}
