<?php

namespace Soosyze\Test;

class AppTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AppCore
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com/?q=index');
        $request = new \Soosyze\Components\Http\ServerRequest('GET', $uri, [], null, '1.1', [
            'SCRIPT_FILENAME' => '/index.php',
            'SCRIPT_NAME'     => '/index.php'
        ]);

        $this->object = AppCore::getInstance($request)->init();
    }

    public function testRun()
    {
        $run = $this->object->run();
        $this->assertEquals($run->getBody()->__toString(), 'ok');
    }

    public function testGetRequest()
    {
        $request = $this->object->getRequest();
        $this->assertInstanceOf('\Psr\Http\Message\RequestInterface', $request);
        $this->assertEquals('GET', $request->getMethod());
    }

    public function testRunJson()
    {
        $this->object->addHook('app.response.before', function (&$request, $response) {
            $uri     = \Soosyze\Components\Http\Uri::create('http://test.com?q=json');
            $request = new \Soosyze\Components\Http\ServerRequest('GET', $uri);
        });
        $this->assertEquals($this->object->run()->getBody()->__toString(), '{"a":1,"b":2,"c":3,"d":4,"e":5}');
    }

    public function testSetSettings()
    {
        $this->object->setSettings([ 'app' => 'tests', 'config' => 'tests/config' ]);
        $this->assertAttributeSame([ 'app' => 'tests', 'config' => 'tests/config' ], 'settings', $this->object);
    }

    public function testGetSettings()
    {
        $this->assertEquals($this->object->getSettings(), [ 'app' => 'tests', 'config' => 'tests/config' ]);
    }

    public function testSetEnvironnement()
    {
        $this->object->setEnvironnement([ 'prod' => [ '' ] ]);
        $this->assertAttributeSame([ 'prod' => [ '' ] ], 'environnement', $this->object);
    }

    public function testGetEnvironnementHostname()
    {
        $this->assertEquals($this->object->getEnvironment(), '');

        $this->object->setEnvironnement([
            'prod'  => [ '' ],
            'local' => [ gethostname() ]
        ]);

        $this->assertEquals($this->object->getEnvironment(), 'local');
    }

    public function testGetEnvironnementAuthority()
    {
        $this->object->setEnvironnement([
            'prod'  => [ '' ],
            'local' => [ 'test.com' ]
        ]);

        $this->assertEquals($this->object->getEnvironment(), 'local');
    }

    public function testIsEnvironnement()
    {
        $this->assertFalse($this->object->isEnvironnement('prod'));
        $this->assertTrue($this->object->isEnvironnement('local'));
    }

    public function testGetDir()
    {
        $this->object->setSettings([
            'root'  => __DIR__,
            'files' => 'files/public'
        ])->setEnvironnement([
            'prod'  => [ gethostname() ],
            'local' => [ '' ]
        ]);

        $test = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/files/public/prod');
        $this->assertEquals($this->object->getDir('files'), $test);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetDirException()
    {
        $this->object->setSettings([
            'files' => [ 'files/public' ]
        ])->getDir('files');
    }

    public function testGetPath()
    {
        $this->object->setSettings([
            'root'  => __DIR__,
            'files' => 'files/public'
        ])->setEnvironnement([
            'prod'  => [ '' ],
            'local' => [ gethostname() ]
        ]);

        $this->assertEquals($this->object->getPath('files'), 'http://test.com/files/public/local');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetPathException()
    {
        $this->object->setSettings([
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
        $this->pathServices = __DIR__ . '/config/testService.json';
    }

    public function index()
    {
        return 'ok';
    }

    public function outputJson()
    {
        return $this->json(200, [ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5 ]);
    }
}
