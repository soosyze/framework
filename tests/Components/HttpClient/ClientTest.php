<?php

namespace Soosyze\Tests\Components\HttpClient;

use Soosyze\Components\Http\Request;
use Soosyze\Components\Http\Uri;
use Soosyze\Components\HttpClient\Client;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = new Client();
    }

    public function testSendRequest()
    {
        $request = new Request(
            'GET',
            Uri::create('https://raw.githubusercontent.com/soosyze/framework/master/composer.json'),
            []
        );

        $response = $this->client->sendRequest($request);

        $body = (string) $response->getBody();

        $this->assertNotNull($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertEquals('text/plain; charset=utf-8', $response->getHeaderLine('content-type'));
    }

    public function testSendRequest400()
    {
        $request = new Request(
            'GET',
            Uri::create('https://raw.githubusercontent.com/soosyze/framework/master'),
            []
        );

        $response = $this->client->sendRequest($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @expectedException \Exception
     */
    public function testSendRequestNetworkException()
    {
        $request = new Request(
            'GET',
            Uri::create('https://fdhgqstedhrfthjdrtutrdyj.com'),
            []
        );

        $this->client->sendRequest($request);
    }
}
