<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Soosyze\Components\HttpFactories\RequestFactory;

class RequestFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RequestFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RequestFactory;
    }

    public function testCreateRequest()
    {
        $request = $this->object->createRequest('GET', 'http://hostname/path');
        $this->assertInstanceOf('\Psr\Http\Message\RequestInterface', $request);
    }
}
