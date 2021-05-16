<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Psr\Http\Message\ResponseInterface;
use Soosyze\Components\HttpFactories\ResponseFactory;

class ResponseFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResponseFactory
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new ResponseFactory;
    }

    public function testCreateResponse(): void
    {
        $response = $this->object->createResponse();
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
