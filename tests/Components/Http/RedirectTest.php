<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Redirect;

class RedirectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Redirect
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Redirect('http://exemple.com');
    }

    public function testSetUpResponse(): void
    {
        $this->assertEquals(301, $this->object->getStatusCode());
        $this->assertEquals('Moved Permanently', $this->object->getReasonPhrase());

        $this->assertEquals(null, (string) $this->object->getBody());
        $this->assertEquals([ 'location' => [ 'http://exemple.com' ] ], $this->object->getHeaders());
    }

    public function testRedirectException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Status code is invalid for redirect.');
        new Redirect('http://exemple.com', 200);
    }
}
