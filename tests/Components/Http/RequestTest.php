<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Request;
use Soosyze\Components\Http\Uri;

class RequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Request
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Request('GET', new Uri());
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @expectedException \Exception
     */
    public function testConstructException()
    {
        new Request('error', new Uri());
    }

    public function testWithMethod()
    {
        $clone = $this->object->withMethod('post');
        $this->assertAttributeSame('POST', 'method', $clone);
    }

    public function testGetMethod()
    {
        $this->assertEquals('GET', $this->object->getMethod());
    }

    /**
     * @expectedException \Exception
     */
    public function testWithMethodException()
    {
        $this->object->withMethod(1);
    }

    public function testGetRequestTargetVoid()
    {
        $target = $this->object->getRequestTarget();
        $this->assertEquals($target, '/');
    }

    public function testGetRequestTargetWithUri()
    {
        $uri   = Uri::create('http://hostname/path');
        $clone = $this->object->withUri($uri);
        $this->assertEquals($clone->getRequestTarget(), '/path');

        $uri    = Uri::create('http://username:password@hostname:80/path/?arg=value#anchor');
        $target = $this->object->withUri($uri)->getRequestTarget();
        $this->assertEquals($target, '/path/?arg=value');
    }

    public function testGetRequestTargetWithTarget()
    {
        $target = $this->object->withRequestTarget('/path')->getRequestTarget();
        $this->assertEquals($target, '/path');
    }

    public function testWithRequestTarget()
    {
        $clone = $this->object->withRequestTarget('/path');
        $this->assertAttributeSame('/path', 'requestTarget', $clone);
    }

    /**
     * @expectedException \Exception
     */
    public function testWithRequestTargetException()
    {
        $this->object->withRequestTarget(0);
    }

    public function testWithUri()
    {
        $uri   = Uri::create('http://hostname/path');
        $clone = $this->object->withUri($uri);
        $this->assertAttributeSame($uri, 'uri', $clone);
    }

    public function testWithUriPreserveHost()
    {
        $uri = Uri::create('http://hostname/path');

        $clone = $this->object->withUri($uri, true);
        $this->assertAttributeSame([ 'host' => [ 'hostname' ] ], 'headers', $clone);

        $clone = $this->object->withHeader('Host', 'other')->withUri($uri, true);
        $this->assertAttributeSame([ 'host' => [ 'other' ] ], 'headers', $clone);
    }
}
