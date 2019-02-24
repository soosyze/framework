<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Soosyze\Components\HttpFactories\ResponseFactory;

class ResponseFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResponseFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ResponseFactory;
    }

    public function testCreateResponse()
    {
        $response = $this->object->createResponse();
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $response);
    }
}
