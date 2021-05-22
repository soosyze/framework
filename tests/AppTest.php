<?php

namespace Soosyze\Tests;

use Psr\Http\Message\RequestInterface;
use Soosyze\Components\Http\ServerRequest;
use Soosyze\Components\Http\Uri;
use Soosyze\Tests\Resources\App\AppCore;

class AppTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AppCore
     */
    protected static $object;

    protected function setUp(): void
    {
        $serverRequest = new ServerRequest(
            'GET',
            Uri::create('http://test.com/index'),
            [],
            null,
            '1.1',
            [
            'SCRIPT_FILENAME' => '/index.php',
            'SCRIPT_NAME'     => '/index.php'
            ]
        );

        self::$object = AppCore::getInstance($serverRequest);
        self::$object->init();
    }

    public function testRun(): void
    {
        $run = self::$object->run();
        $this->assertEquals('ok', (string) $run->getBody());
    }

    public function testGetRequest(): void
    {
        $request = self::$object->getRequest();

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals('GET', $request->getMethod());
    }

    public function testRunJson(): void
    {
        self::$object->addHook('app.response.before', function (&$request, $response) {
            $uri     = Uri::create('http://test.com/json');
            $request = new ServerRequest('GET', $uri);
        });

        $this->assertEquals('{"a":1,"b":2,"c":3,"d":4,"e":5}', (string) self::$object->run()->getBody());
    }

    public function testGetSettings(): void
    {
        self::$object->setSettings([ 'app' => 'tests', 'config' => 'tests/config' ]);

        $this->assertEquals(
            [ 'app' => 'tests', 'config' => 'tests/config' ],
            self::$object->getSettings()
        );
    }

    public function testGetEnvironnementHostname(): void
    {
        $this->assertEquals(self::$object->getEnvironment(), '');

        self::$object->setEnvironnement([
            'prod'  => [ '' ],
            'local' => [ gethostname() ]
        ]);

        $this->assertEquals('local', self::$object->getEnvironment());
    }

    public function testGetEnvironnementAuthority(): void
    {
        self::$object->setEnvironnement([
            'prod'  => [ '' ],
            'local' => [ 'test.com' ]
        ]);

        $this->assertEquals('local', self::$object->getEnvironment());
    }

    public function testIsEnvironnement(): void
    {
        $this->assertFalse(self::$object->isEnvironnement('prod'));
        $this->assertTrue(self::$object->isEnvironnement('local'));
    }

    public function testGetDir(): void
    {
        self::$object->setSettings([
            'root'  => __DIR__,
            'files' => 'files/public'
        ])->setEnvironnement([
            'prod'  => [ gethostname() ],
            'local' => [ '' ]
        ]);

        $expectedDir = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/files/public/prod');
        $this->assertEquals($expectedDir, self::$object->getDir('files'));
    }

    public function testGetDirException(): void
    {
        self::$object->setSettings(['files' => [ 'files/public' ] ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectErrorMessage('The framework parameter must return a string.');
        self::$object->getDir('files');
    }

    public function testGetPath(): void
    {
        self::$object->setSettings([
            'root'  => __DIR__,
            'files' => 'files/public'
        ])->setEnvironnement([
            'prod'  => [ '' ],
            'local' => [ gethostname() ]
        ]);

        $this->assertEquals('http://test.com/files/public/local', self::$object->getPath('files'));
    }

    public function testGetPathException(): void
    {
        self::$object->setSettings([ 'files' => [ 'files/public' ] ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectErrorMessage('The framework parameter must return a string.');
        self::$object->getPath('files');
    }
}
