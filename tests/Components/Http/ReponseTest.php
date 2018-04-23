<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Reponse,
    Soosyze\Components\Http\Stream;

class ReponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reponse
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Reponse;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    public function testSetUpReponse()
    {
        $this->assertAttributeSame(200, 'code', $this->object);
        $this->assertAttributeSame('OK', 'reasonPhrase', $this->object);
        $this->assertAttributeSame(null, 'body', $this->object);
        $this->assertAttributeSame([], 'headers', $this->object);
    }

    public function testConstructReponse()
    {
        $rep = new Reponse(404, new Stream('Page not found, sorry'), [ 'Localtion' => '/error' ]);
        $this->assertAttributeSame(404, 'code', $rep);
        $this->assertAttributeSame('Not Found', 'reasonPhrase', $rep);
        $this->assertEquals('Page not found, sorry', ( string ) $rep->getBody());
        $this->assertAttributeSame([ 'Localtion' => '/error' ], 'headers', $rep);
    }

    public function testConstructReponsewithCodeNumeric()
    {
        $rep = new Reponse('300');
        $this->assertAttributeSame(300, 'code', $rep);
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

    public function testWithStatusNumeric()
    {
        $clone = $this->object->withStatus('300');
        $this->assertAttributeSame(300, 'code', $clone);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWithStatusException()
    {
        $this->object->withStatus('error');
    }

    public function testWithStatusAndReasonPhrase()
    {
        $clone = $this->object->withStatus(404, 'Not Found perso');
        $this->assertAttributeSame(404, 'code', $clone);
        $this->assertAttributeSame('Not Found perso', 'reasonPhrase', $clone);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWithStatusAndReasonPhraseException()
    {
        $this->object->withStatus(400, 1);
    }
}