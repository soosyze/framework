<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Psr\Http\Message\ServerRequestInterface;
use Soosyze\Components\HttpFactories\ServerRequestFactory;

class ServerRequestFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ServerRequestFactory
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new ServerRequestFactory;
    }

    public function testCreateServerRequest(): void
    {
        $serverRequest = $this->object->createServerRequest('GET', 'http://foo.com/path');
        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
        $this->assertEquals('GET', $serverRequest->getMethod());

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $serverRequest = $this->object->createServerRequest('', 'http://foo.com/path', $_SERVER);
        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
        $this->assertEquals('POST', $serverRequest->getMethod());
    }
}
