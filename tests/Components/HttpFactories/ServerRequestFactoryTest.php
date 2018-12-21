<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Soosyze\Components\HttpFactories\ServerRequestFactory;

class ServerRequestFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ServerRequestFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ServerRequestFactory;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testCreateServerRequest()
    {
        $serverRequest = $this->object->createServerRequest('GET', 'http://hostname/path');
        $this->assertInstanceOf('\Psr\Http\Message\ServerRequestInterface', $serverRequest);
    }
}
