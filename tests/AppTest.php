<?php

namespace Soosyze\Tests;

class AppTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AppCore
     */
    protected static $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public static function setUpBeforeClass()
    {
        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com/?q=index');
        $request = new \Soosyze\Components\Http\ServerRequest(
            'GET',
            $uri,
            [],
            null,
            '1.1',
            [
            'SCRIPT_FILENAME' => '/index.php',
            'SCRIPT_NAME'     => '/index.php'
        ]
        );

        self::$object = AppCore::getInstance($request)->init();
    }

    public function testRun()
    {
        $run = self::$object->run();
        $this->assertEquals($run->getBody()->__toString(), 'ok');
    }

    public function testGetRequest()
    {
        $request = self::$object->getRequest();
        $this->assertInstanceOf('\Psr\Http\Message\RequestInterface', $request);
        $this->assertEquals('GET', $request->getMethod());
    }

    public function testRunJson()
    {
        self::$object->addHook('app.response.before', function (&$request, $response) {
            $uri     = \Soosyze\Components\Http\Uri::create('http://test.com?q=json');
            $request = new \Soosyze\Components\Http\ServerRequest('GET', $uri);
        });
        $this->assertEquals(self::$object->run()->getBody()->__toString(), '{"a":1,"b":2,"c":3,"d":4,"e":5}');
    }

    public function testSetSettings()
    {
        self::$object->setSettings([ 'app' => 'tests', 'config' => 'tests/config' ]);
        $this->assertAttributeSame([ 'app' => 'tests', 'config' => 'tests/config' ], 'settings', self::$object);
    }

    public function testGetSettings()
    {
        $this->assertEquals(self::$object->getSettings(), [ 'app' => 'tests', 'config' => 'tests/config' ]);
    }

    public function testSetEnvironnement()
    {
        self::$object->setEnvironnement([ 'prod' => [ '' ] ]);
        $this->assertAttributeSame([ 'prod' => [ '' ] ], 'environnement', self::$object);
    }

    public function testGetEnvironnementHostname()
    {
        $this->assertEquals(self::$object->getEnvironment(), '');

        self::$object->setEnvironnement([
            'prod'  => [ '' ],
            'local' => [ gethostname() ]
        ]);

        $this->assertEquals(self::$object->getEnvironment(), 'local');
    }

    public function testGetEnvironnementAuthority()
    {
        self::$object->setEnvironnement([
            'prod'  => [ '' ],
            'local' => [ 'test.com' ]
        ]);

        $this->assertEquals(self::$object->getEnvironment(), 'local');
    }

    public function testIsEnvironnement()
    {
        $this->assertFalse(self::$object->isEnvironnement('prod'));
        $this->assertTrue(self::$object->isEnvironnement('local'));
    }

    public function testGetDir()
    {
        self::$object->setSettings([
            'root'  => __DIR__,
            'files' => 'files/public'
        ])->setEnvironnement([
            'prod'  => [ gethostname() ],
            'local' => [ '' ]
        ]);

        $test = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/files/public/prod');
        $this->assertEquals(self::$object->getDir('files'), $test);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetDirException()
    {
        self::$object->setSettings([
            'files' => [ 'files/public' ]
        ])->getDir('files');
    }

    public function testGetPath()
    {
        self::$object->setSettings([
            'root'  => __DIR__,
            'files' => 'files/public'
        ])->setEnvironnement([
            'prod'  => [ '' ],
            'local' => [ gethostname() ]
        ]);

        $this->assertEquals(self::$object->getPath('files'), 'http://test.com/files/public/local');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetPathException()
    {
        self::$object->setSettings([
            'files' => [ 'files/public' ]
        ])->getPath('files');
    }
}

class AppCore extends \Soosyze\App
{
    protected function loadModules()
    {
        return [
            new TestModule()
        ];
    }

    protected function loadServices()
    {
        return [];
    }
}

class TestModule extends \Soosyze\Controller
{
    public function __construct()
    {
        $this->pathRoutes   = __DIR__ . '/config/routes.php';
        $this->pathServices = __DIR__ . '/config/services.php';
    }

    public function index()
    {
        return 'ok';
    }

    public function api()
    {
        return $this->json(200, [ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5 ]);
    }
}
