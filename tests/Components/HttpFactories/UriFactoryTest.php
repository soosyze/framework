<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Soosyze\Components\HttpFactories\UriFactory;

class UriFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var UriFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new UriFactory;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Soosyze\Components\HttpFactories\UriFactory::createUri
     * @todo   Implement testCreateUri().
     */
    public function testCreateUri()
    {
        $uri = $this->object->createUri('http://username:password@hostname:80/path?arg=value#anchor');
        $this->assertInstanceOf('\Psr\Http\Message\UriInterface', $uri);
    }
}
