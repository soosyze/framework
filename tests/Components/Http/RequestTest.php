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

    protected function setUp(): void
    {
        $this->object = new Request('GET', new Uri());
    }

    public function testConstructException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectErrorMessage('The method is not valid (only CONNECT|DELETE|GET|HEAD|OPTIONS|PATCH|POST|PURGE|PUT|TRACE).');
        new Request('error', new Uri());
    }

    public function testWithMethod(): void
    {
        $this->assertEquals('POST', $this->object->withMethod('POST')->getMethod());
        $this->assertEquals('post', $this->object->withMethod('post')->getMethod());
    }

    public function testGetMethod(): void
    {
        $this->assertEquals('GET', $this->object->getMethod());
    }

    /**
     * @dataProvider providerInvalidMethods
     *
     * @param mixed $method
     */
    public function testWithMethodException($method): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectErrorMessage('The method must be a string');
        $this->object->withMethod($method);
    }

    public function providerInvalidMethods(): \Generator
    {
        yield [ null ];
        yield [ 1 ];
        yield [ 1.01 ];
        yield [ false ];
        yield [ [ 'foo' ] ];
        yield [ new \stdClass() ];
    }

    public function testGetRequestTargetVoid(): void
    {
        $target = $this->object->getRequestTarget();
        $this->assertEquals('/', $target);
    }

    public function testGetRequestTargetWithUri(): void
    {
        $uri   = Uri::create('http://hostname/path');
        $clone = $this->object->withUri($uri);
        $this->assertEquals('/path', $clone->getRequestTarget());

        $uri    = Uri::create('http://username:password@hostname:80/path/?arg=value#anchor');
        $target = $this->object->withUri($uri)->getRequestTarget();
        $this->assertEquals('/path/?arg=value', $target);
    }

    public function testGetRequestTargetWithTarget(): void
    {
        $target = $this->object->withRequestTarget('/path')->getRequestTarget();
        $this->assertEquals('/path', $target);
    }

    public function testWithRequestTarget(): void
    {
        $clone = $this->object->withRequestTarget('/path');
        $this->assertEquals('/path', $clone->getRequestTarget());
    }

    public function testWithRequestTargetException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectErrorMessage('The target of the request must be a string.');
        $this->object->withRequestTarget(0);
    }

    public function testWithUri(): void
    {
        $uri   = Uri::create('http://hostname/path');
        $clone = $this->object->withUri($uri);
        $this->assertEquals($uri, $clone->getUri());
    }

    public function testWithUriPreserveHost(): void
    {
        $uri = Uri::create('http://hostname/path');

        $clone = $this->object->withUri($uri, true);
        $this->assertEquals([ 'Host' => [ 'hostname' ] ], $clone->getHeaders());
        $this->assertEquals('hostname', $clone->getHeaderLine('host'));

        $clone = $this->object->withHeader('Host', 'other')->withUri($uri, true);
        $this->assertEquals([ 'Host' => [ 'other' ] ], $clone->getHeaders());
    }
}
