<?php

namespace Soosyze\Tests;

use Soosyze\Components\Http\Response;
use Soosyze\Components\Http\Stream;
use Soosyze\ResponseEmitter;

require_once __DIR__ . '/Resources/Functions.php';

class ResponseEmitterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResponseEmitter
     */
    protected $object;

    public function setUp(): void
    {
        $this->object = new ResponseEmitter();
    }

    public function testEmit(): void
    {
        $response = new Response(
            404,
            new Stream('Page not found, sorry'),
            [ 'Localtion' => '/error' ]
        );

        $responseEmit = $this->object->emit($response);

        $this->assertEquals('Page not found, sorry', $responseEmit);
        $this->assertEquals(
            [
                [ 'HTTP/1.0 404 Not Found', true, 404 ],
                [ 'Localtion: /error', true, null ]
            ],
            \Soosyze\Output::$headers
        );
    }
}
