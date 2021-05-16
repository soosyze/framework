<?php

namespace Soosyze\Tests\Components\HttpFactories;

use Psr\Http\Message\UriInterface;
use Soosyze\Components\HttpFactories\UriFactory;

class UriFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var UriFactory
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new UriFactory;
    }

    public function testCreateUri(): void
    {
        $uri = $this->object->createUri('http://username:password@hostname:80/path?arg=value#anchor');
        $this->assertInstanceOf(UriInterface::class, $uri);
    }
}
