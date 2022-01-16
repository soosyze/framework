<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Response;
use Soosyze\Components\Http\Stream;

require_once __DIR__ . '/../../Resources/Functions.php';

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Response
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Response;
    }

    public function testSetUpResponse(): void
    {
        $this->assertEquals(200, $this->object->getStatusCode());
        $this->assertEquals('OK', $this->object->getReasonPhrase());
        $this->assertEquals('', (string) $this->object->getBody());
        $this->assertEquals([], $this->object->getHeaders());
    }

    public function testConstructResponse(): void
    {
        $rep = new Response(
            404,
            new Stream('Page not found, sorry'),
            [ 'localtion' => [ '/error' ] ],
            'Page not found'
        );

        $this->assertEquals(404, $rep->getStatusCode());
        $this->assertEquals('Page not found', $rep->getReasonPhrase());
        $this->assertEquals('Page not found, sorry', (string) $rep->getBody());
        $this->assertEquals([ 'localtion' => [ '/error' ] ], $rep->getHeaders());
    }

    public function testWithStatus(): void
    {
        $clone = $this->object->withStatus(404);

        $this->assertEquals(404, $clone->getStatusCode());
        $this->assertEquals('Not Found', $clone->getReasonPhrase());
    }

    /**
     * @dataProvider providerInvalidStatusCodeArguments
     *
     * @param mixed $code
     * @param mixed $reasonPhrase
     */
    public function testWithStatusInvalidArgumentException($code, $reasonPhrase): void
    {
        $this->expectException(\InvalidArgumentException::class);
        /** @phpstan-ignore-next-line */
        $this->object->withStatus($code, $reasonPhrase);
    }

    public function providerInvalidStatusCodeArguments(): \Generator
    {
        yield [ true, '' ];
        yield [ 'foobar', '' ];
        yield [ 99, '' ];
        yield [ 600, '' ];
        yield [ 200.34, '' ];
        yield [ new \stdClass(), '' ];
        yield [ 400, 1 ];
    }

    public function testWithStatusAndReasonPhrase(): void
    {
        $clone = $this->object->withStatus(404, 'Not Found perso');

        $this->assertEquals(404, $clone->getStatusCode());
        $this->assertEquals('Not Found perso', $clone->getReasonPhrase());
    }

    public function testToString(): void
    {
        $rep = new Response(404, new Stream('Page not found, sorry'), [ 'Localtion' => '/error' ]);

        $this->assertEquals('Page not found, sorry', (string) $rep);
        $this->assertEquals(
            [
                'HTTP/1.0 404 Not Found',
                'Localtion: /error'
            ],
            \Soosyze\Components\Http\Output::$headers
        );
    }
}
