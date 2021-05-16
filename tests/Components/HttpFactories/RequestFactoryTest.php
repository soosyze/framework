<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Psr\Http\Message\RequestInterface;
use Soosyze\Components\HttpFactories\RequestFactory;

class RequestFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RequestFactory
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new RequestFactory;
    }

    public function testCreateRequest(): void
    {
        $request = $this->object->createRequest('GET', 'http://hostname/path');
        $this->assertInstanceOf(RequestInterface::class, $request);
    }
}
