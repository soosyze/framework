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
     * @expectedException \Exception
     */
    public function testConstructException()
    {
        new Request('error', new Uri());
    }

    public function testWithMethod()
    {
        $clone = $this->object->withMethod('POST');
        $this->assertAttributeSame('POST', 'method', $clone);
        $clone = $this->object->withMethod('post');
        $this->assertAttributeSame('post', 'method', $clone);
    }

    public function testGetMethod()
    {
        $this->assertEquals('GET', $this->object->getMethod());
    }

    /**
     * @dataProvider getInvalidMethods
     * @expectedException \Exception
     */
    public function testWithMethodException($code)
    {
        $this->object->withMethod($code);
    }

    public function getInvalidMethods()
    {
        return [
            [null],
            [1],
            [1.01],
            [false],
            [['foo']],
            [new \stdClass()],
        ];
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
        $this->assertAttributeSame([ 'Host' => [ 'hostname' ] ], 'headers', $clone);
        $this->assertEquals('hostname', $clone->getHeaderLine('host'));

        $clone = $this->object->withHeader('Host', 'other')->withUri($uri, true);
        $this->assertAttributeSame([ 'Host' => [ 'other' ] ], 'headers', $clone);
    }
}
