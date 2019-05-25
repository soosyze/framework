<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Response;
use Soosyze\Components\Http\Stream;

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Response
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Response;
    }

    public function testSetUpResponse()
    {
        $this->assertAttributeSame(200, 'code', $this->object);
        $this->assertAttributeSame('OK', 'reasonPhrase', $this->object);
        $this->assertAttributeSame(null, 'body', $this->object);
        $this->assertAttributeSame([], 'headers', $this->object);
    }

    public function testConstructResponse()
    {
        $rep = new Response(404, new Stream('Page not found, sorry'), [ 'Localtion' => ['/error'] ]);
        $this->assertAttributeSame(404, 'code', $rep);
        $this->assertAttributeSame('Not Found', 'reasonPhrase', $rep);
        $this->assertEquals('Page not found, sorry', (string) $rep->getBody());
        $this->assertAttributeSame([ 'localtion' => ['/error'] ], 'headers', $rep);
    }

    public function testGetStatusCode()
    {
        $this->assertEquals($this->object->getStatusCode(), 200);
    }

    public function testGetReasonPhrase()
    {
        $this->assertEquals($this->object->getReasonPhrase(), 'OK');
    }

    public function testWithStatus()
    {
        $clone = $this->object->withStatus(404);
        $this->assertAttributeSame(404, 'code', $clone);
        $this->assertAttributeSame('Not Found', 'reasonPhrase', $clone);
    }

    /**
     * @dataProvider getInvalidStatusCodeArguments
     * @expectedException \InvalidArgumentException
     */
    public function testWithStatusException($code)
    {
        $this->object->withStatus($code);
    }

    public function getInvalidStatusCodeArguments()
    {
        return [
            [true],
            ['foobar'],
            [99],
            [600],
            [200.34],
            [new \stdClass()],
        ];
    }

    public function testWithStatusAndReasonPhrase()
    {
        $clone = $this->object->withStatus(404, 'Not Found perso');
        $this->assertAttributeSame(404, 'code', $clone);
        $this->assertAttributeSame('Not Found perso', 'reasonPhrase', $clone);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithStatusAndReasonPhraseException()
    {
        $this->object->withStatus(400, 1);
    }
    
    /*
     * @runInSeparateProcess
     */
//    public function testToString()
//    {
//        $rep = new Response(404, new Stream('Page not found, sorry'), [ 'Localtion' => '/error' ]);
//        $this->assertEquals('Page not found, sorry', (string) $rep);
//    }
}
