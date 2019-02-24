<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Redirect;

class RedirectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Redirect
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Redirect('http://exemple.com');
    }

    public function testSetUpResponse()
    {
        $this->assertAttributeSame(301, 'code', $this->object);
        $this->assertAttributeSame('Moved Permanently', 'reasonPhrase', $this->object);
        $this->assertAttributeSame(null, 'body', $this->object);
        $this->assertAttributeSame([ 'location' => [ 'http://exemple.com' ] ], 'headers', $this->object);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRedirectException()
    {
        new Redirect('http://exemple.com', 200);
    }
}
