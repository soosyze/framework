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
        $uri     = \Soosyze\Components\Http\Uri::create('http://test.com?index');
        $request = new \Soosyze\Components\Http\ServerRequest('GET', $uri);

        $this->object = AppCore::getInstance($request)->init();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testRun()
    {
        $run = $this->object->run();
        $this->assertEquals($run->getBody()->__toString(), 'ok');
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

    public function testGetEnvironnement()
    {
        $this->assertEquals($this->object->getEnvironment(), '');

        $this->object->setEnvironnement([
            'prod'  => [ '' ],
            'local' => [ gethostname() ]
        ]);

        $this->assertEquals($this->object->getEnvironment(), 'local');
    }

    public function testIsEnvironnement()
    {
        $this->assertFalse($this->object->isEnvironnement('prod'));
        $this->assertTrue($this->object->isEnvironnement('local'));
    }
}

class AppCore extends \Soosyze\App
{
    protected function loadModules()
    {
        return [
            'Test' => new TestModule()
        ];
    }

    protected function loadServices()
    {
        return [];
    }
}

class TestModule extends \Soosyze\Controller
{
    protected $pathRoutes = __DIR__ . '/config/testRouting.json';

    protected $pathServices = __DIR__ . '/config/testService.json';

    public function index()
    {
        return 'ok';
    }
}
