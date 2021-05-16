<?php

namespace Soosyze\Tests\Components\Http;

use Psr\Http\Message\UploadedFileInterface;
use Soosyze\Components\Http\ServerRequest;
use Soosyze\Components\Http\UploadedFile;

class ServerRequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ServerRequest
     */
    protected $object;

    /**
     * Représentation de la variable superglobale $_FILES.
     *
     * @var array
     */
    protected $filesTest = [
        'file'  => [
            'name'     => '',
            'type'     => '',
            'tmp_name' => '',
            'error'    => 4,
            'size'     => 0
        ],
        'file2' => [
            'name'     => '',
            'type'     => '',
            'tmp_name' => '',
            'error'    => 4,
            'size'     => 0 ],
        'file3' => [
            'name'     => [
                0 => '',
                1 => ''
            ],
            'type'     => [
                0 => '',
                1 => ''
            ],
            'tmp_name' => [
                0 => '',
                1 => ''
            ],
            'error'    => [
                0 => 4,
                1 => 4
            ],
            'size'     => [
                0 => 0,
                1 => 0
            ]
        ],
        'file4' => [
            'name'     => [
                'details' => [
                    0         => '',
                    1         => '',
                    'avatars' => [
                        0 => '',
                        1 => ''
                    ]
                ]
            ],
            'type'     => [
                'details' => [
                    0         => '',
                    1         => '',
                    'avatars' => [
                        0 => '',
                        1 => ''
                    ]
                ]
            ],
            'tmp_name' => [
                'details' => [
                    0         => '',
                    1         => '',
                    'avatars' => [
                        0 => '',
                        1 => ''
                    ]
                ]
            ],
            'error'    => [
                'details' => [
                    0         => 4,
                    1         => 4,
                    'avatars' => [
                        0 => 4,
                        1 => 4
                    ]
                ]
            ],
            'size'     => [
                'details' => [
                    0         => 0,
                    1         => 0,
                    'avatars' => [
                        0 => 0,
                        1 => 0
                    ]
                ]
            ]
        ]
    ];

    /**
     * @var array
     */
    protected $resultFiles = [
        'file'  => [
            'name'     => '',
            'type'     => '',
            'tmp_name' => '',
            'error'    => 4,
            'size'     => 0
        ],
        'file2' => [
            'name'     => '',
            'type'     => '',
            'tmp_name' => '',
            'error'    => 4,
            'size'     => 0
        ],
        'file3' => [
            0 => [
                'name'     => '',
                'type'     => '',
                'tmp_name' => '',
                'error'    => 4,
                'size'     => 0
            ],
            1 => [
                'name'     => '',
                'type'     => '',
                'tmp_name' => '',
                'error'    => 4,
                'size'     => 0
            ]
        ],
        'file4' => [
            'details' => [
                0         => [
                    'name'     => '',
                    'type'     => '',
                    'tmp_name' => '',
                    'error'    => 4,
                    'size'     => 0
                ],
                1         => [
                    'name'     => '',
                    'type'     => '',
                    'tmp_name' => '',
                    'error'    => 4,
                    'size'     => 0
                ],
                'avatars' => [
                    0 => [
                        'name'     => '',
                        'type'     => '',
                        'tmp_name' => '',
                        'error'    => 4,
                        'size'     => 0
                    ],
                    1 => [
                        'name'     => '',
                        'type'     => '',
                        'tmp_name' => '',
                        'error'    => 4,
                        'size'     => 0
                    ]
                ]
            ]
        ]
    ];

    protected function setUp(): void
    {
        $method       = 'GET';
        $uri          = \Soosyze\Components\Http\Uri::create('http://exemple.com?key=value');
        $headers      = [];
        $body         = new \Soosyze\Components\Http\Stream();
        $version      = '1.1';
        $serverParams = [];
        $cookies      = [];
        $uploadFiles  = [];

        $this->object = new ServerRequest(
            $method,
            $uri,
            $headers,
            $body,
            $version,
            $serverParams,
            $cookies,
            $uploadFiles
        );
    }

    public function testCreate(): void
    {
        $_SERVER[ 'HTTP_HOST' ]       = 'exemple.com';
        $_SERVER[ 'REQUEST_URI' ]     = '/test/index.php/other';
        $_SERVER[ 'REQUEST_METHOD' ]  = 'GET';
        $_SERVER[ 'SCRIPT_FILENAME' ] = '/test/index.php';
        $_SERVER[ 'SCRIPT_NAME' ]     = '/test/index.php';

        $request = ServerRequest::create();

        $this->assertEquals($_SERVER, $request->getServerParams());
        $this->assertEquals('http://exemple.com/test/', $request->getBasePath());
    }

    public function testGetServerParams(): void
    {
        $this->assertEquals([], $this->object->getServerParams());
    }

    public function testGetCookieParams(): void
    {
        $this->assertEquals([], $this->object->getCookieParams());
    }

    public function testWithCookieParams(): void
    {
        $clone = $this->object->withCookieParams([ 'cookie_key' => 'cookie_value' ]);

        $this->assertEquals([ 'cookie_key' => 'cookie_value' ], $clone->getCookieParams());
    }

    public function testGetQueryParams(): void
    {
        $this->assertEquals([], $this->object->getQueryParams());
    }

    public function testWithQueryParams(): void
    {
        $clone = $this->object->withQueryParams([ 'key' => 'value' ]);

        $this->assertEquals([ 'key' => 'value' ], $clone->getQueryParams());
    }

    public function testGetUploadedFiles(): void
    {
        $this->assertEquals([], $this->object->getUploadedFiles());
    }

    public function testParseFiles(): void
    {
        $parseFiles = ServerRequest::parseFiles($this->filesTest);

        $this->assertEquals($this->resultFiles, $parseFiles);
    }

    public function testWithUploadedFiles(): void
    {
        $clone = $this->object->withUploadedFiles($this->filesTest);

        $file    = $clone->getUploadedFiles()[ 'file' ];
        $file2   = $clone->getUploadedFiles()[ 'file2' ];
        $file3   = $clone->getUploadedFiles()[ 'file3' ][ 0 ];
        $file4   = $clone->getUploadedFiles()[ 'file4' ][ 'details' ][ 0 ];
        $file4_1 = $clone->getUploadedFiles()[ 'file4' ][ 'details' ][ 'avatars' ][ 0 ];

        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertInstanceOf(UploadedFileInterface::class, $file2);
        $this->assertInstanceOf(UploadedFileInterface::class, $file3);
        $this->assertInstanceOf(UploadedFileInterface::class, $file4);
        $this->assertInstanceOf(UploadedFileInterface::class, $file4_1);
    }

    public function testWithUploadedFilesUpload(): void
    {
        $upl   = [ new UploadedFile('') ];
        $clone = $this->object->withUploadedFiles($upl);

        $this->assertEquals($upl, $clone->getUploadedFiles());
    }

    public function testWithUploadedFilesException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectErrorMessage('The input parameter is not in the correct format.');
        $this->object->withUploadedFiles([ 'tmp_name' => '' ]);
    }

    public function testGetParseBody(): void
    {
        $this->assertEquals(null, $this->object->getParsedBody());
    }

    public function testWithParseBody(): void
    {
        $clone = $this->object->withParsedBody([ 'key' => 'value' ]);

        $this->assertEquals([ 'key' => 'value' ], $clone->getParsedBody());
    }

    public function testGetAttributes(): void
    {
        $this->assertEquals([], $this->object->getAttributes());
    }

    public function testGetAttribute(): void
    {
        $this->assertEquals('default', $this->object->getAttribute('key', 'default'));
    }

    public function testWithAttribute(): void
    {
        $clone = $this->object->withAttribute('key', 'value');

        $this->assertEquals([ 'key' => 'value' ], $clone->getAttributes());
        $this->assertEquals('value', $clone->getAttribute('key'));
    }

    public function testWithoutAttribute(): void
    {
        $clone = $this->object
            ->withAttribute('key', 'value')
            ->withAttribute('key2', 'value2');
        $this->assertEquals([ 'key' => 'value', 'key2' => 'value2' ], $clone->getAttributes());

        $clone2 = $clone->withoutAttribute('key');
        $this->assertEquals([ 'key2' => 'value2' ], $clone2->getAttributes());
    }

    /**
     * @dataProvider providerParsedBodyException
     *
     * @param mixed $data
     */
    public function testParsedBodyException($data): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectErrorMessage('First parameter to withParsedBody MUST be object, array or null.');
        $this->object->withParsedBody($data);
    }

    public function providerParsedBodyException(): \Generator
    {
        yield [ 4711 ];
        yield [ 47.11 ];
        yield [ 'foobar' ];
        yield [ true ];
    }
}
