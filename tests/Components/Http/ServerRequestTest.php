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
        $_FILES  = [];
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
    
    public function testParseFiles()
    {
        $output = ServerRequest::parseFiles($this->filesTest);

        $this->assertEquals($output, $this->resultFiles);
    }

    public function testWithUploadedFiles()
    {
        $clone = $this->object->withUploadedFiles($this->filesTest);

        $file    = $clone->getUploadedFiles()[ 'file' ];
        $file2   = $clone->getUploadedFiles()[ 'file2' ];
        $file3   = $clone->getUploadedFiles()[ 'file3' ][ 0 ];
        $file4   = $clone->getUploadedFiles()[ 'file4' ][ 'details' ][ 0 ];
        $file4_1 = $clone->getUploadedFiles()[ 'file4' ][ 'details' ][ 'avatars' ][ 0 ];

        $this->assertInstanceOf('\Psr\Http\Message\UploadedFileInterface', $file);
        $this->assertInstanceOf('\Psr\Http\Message\UploadedFileInterface', $file2);
        $this->assertInstanceOf('\Psr\Http\Message\UploadedFileInterface', $file3);
        $this->assertInstanceOf('\Psr\Http\Message\UploadedFileInterface', $file4);
        $this->assertInstanceOf('\Psr\Http\Message\UploadedFileInterface', $file4_1);
    }

    public function testWithUploadedFilesUpload()
    {
        $upl   = new \Soosyze\Components\Http\UploadedFile('');
        $clone = $this->object->withUploadedFiles([ $upl ]);
        $this->assertAttributeSame([ $upl ], 'uploadFiles', $clone);
    }

    /**
     * @expectedException \Exception
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
