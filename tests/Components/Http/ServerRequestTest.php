<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\ServerRequest;

class ServerRequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var UplaodeFile
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $method       = 'GET';
        $uri          = \Soosyze\Components\Http\Uri::create('http://exemple.com?key=value');
        $headers      = [];
        $body         = new \Soosyze\Components\Http\Stream();
        $version      = '1.1';
        $serverParams = [];
        $cookies      = [];
        $uploadFiles  = [];

        $this->object = new ServerRequest($method, $uri, $headers, $body, $version, $serverParams, $cookies, $uploadFiles);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testCreate()
    {
        $_SERVER = [
            'HTTP_HOST'      => 'exemple.com',
            'REQUEST_URI'    => '/test',
            'REQUEST_METHOD' => 'GET'
        ];
        $request = ServerRequest::create();
        $_FILES = [];
        $this->assertAttributeSame($_SERVER, 'serverParams', $request);
    }

    public function testAttributes()
    {
        $this->assertAttributeSame([], 'serverParams', $this->object);
        $this->assertAttributeSame([], 'cookieParams', $this->object);
        $this->assertAttributeSame([], 'uploadFiles', $this->object);
    }

    public function testGetServerParams()
    {
        $this->assertEquals([], $this->object->getServerParams());
    }

    public function testGetCookieParams()
    {
        $this->assertEquals([], $this->object->getCookieParams());
    }

    public function testWithCookieParams()
    {
        $clone = $this->object->withCookieParams([ 'cookie_key' => 'cookie_value' ]);
        $this->assertAttributeSame([ 'cookie_key' => 'cookie_value' ], 'cookieParams', $clone);
    }

    public function testGetQueryParams()
    {
        $this->assertEquals([], $this->object->getQueryParams());
    }

    public function testWithQueryParams()
    {
        $clone = $this->object->withQueryParams([ 'key' => 'value' ]);
        $this->assertAttributeSame([ 'key' => 'value' ], 'queryParams', $clone);
    }

    public function testGetUploadedFiles()
    {
        $this->assertEquals([], $this->object->getUploadedFiles());
    }

    public function testWithUploadedFiles()
    {
        $upl   = new \Soosyze\Components\Http\UploadedFile('');
        $clone = $this->object->withUploadedFiles([ $upl ]);
        $this->assertAttributeSame([ $upl ], 'uploadFiles', $clone);
    }

    /**
     * @expectedException Exception
     */
    public function testWithUploadedFilesException()
    {
        $this->object->withUploadedFiles([ '' ]);
    }

    public function testGetParseBody()
    {
        $this->assertEquals(null, $this->object->getParsedBody());
    }

    public function testWithParseBody()
    {
        $clone = $this->object->withParsedBody([ 'key' => 'value' ]);
        $this->assertAttributeSame([ 'key' => 'value' ], 'parseBody', $clone);
    }

    public function testGetAttributes()
    {
        $this->assertEquals([], $this->object->getAttributes());
    }

    public function testGetAttribute()
    {
        $this->assertEquals('default', $this->object->getAttribute('key', 'default'));
    }

    public function testWithAttribute()
    {
        $clone = $this->object->withAttribute('key', 'value');

        $this->assertAttributeSame([ 'key' => 'value' ], 'attributes', $clone);
        $this->assertEquals('value', $clone->getAttribute('key'));
    }

    public function testWithoutAttribute()
    {
        $clone = $this->object->withAttribute('key', 'value')
            ->withAttribute('key2', 'value2');
        $this->assertAttributeSame([ 'key' => 'value', 'key2' => 'value2' ], 'attributes', $clone);

        $clone2 = $clone->withoutAttribute('key');
        $this->assertAttributeSame([ 'key2' => 'value2' ], 'attributes', $clone2);
    }
}
