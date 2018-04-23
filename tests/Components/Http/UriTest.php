<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Uri;

class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Uri
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Uri('http', 'hostname', '/path', 80, 'arg=value', 'anchor', 'username', 'password');
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
        $this->assertAttributeSame('http', 'scheme', $this->object);
        $this->assertAttributeSame('username', 'user', $this->object);
        $this->assertAttributeSame('password', 'password', $this->object);
        $this->assertAttributeSame('hostname', 'host', $this->object);
        $this->assertAttributeSame(null, 'port', $this->object);
        $this->assertAttributeSame('/path', 'path', $this->object);
        $this->assertAttributeSame('arg=value', 'query', $this->object);
        $this->assertAttributeSame('anchor', 'fragment', $this->object);
    }

    public function testCreateUri()
    {
        $uri = Uri::create('http://username:password@hostname:80/path?arg=value#anchor');

        $this->assertAttributeSame('http', 'scheme', $uri);
        $this->assertAttributeSame('username', 'user', $uri);
        $this->assertAttributeSame('password', 'password', $uri);
        $this->assertAttributeSame('hostname', 'host', $uri);
        $this->assertAttributeSame(null, 'port', $uri);
        $this->assertAttributeSame('/path', 'path', $uri);
        $this->assertAttributeSame('arg=value', 'query', $uri);
        $this->assertAttributeSame('anchor', 'fragment', $uri);
    }

    public function testGetScheme()
    {
        $this->assertEquals($this->object->getScheme(), 'http');
    }

    public function testGetAuthority()
    {
        $uri = Uri::create('http://hostname');
        $this->assertEquals($uri->getAuthority(), 'hostname');
    }

    public function testGetAuthorityWithUser()
    {
        $uri = Uri::create('http://username@hostname');
        $this->assertEquals($uri->getAuthority(), 'username@hostname');
    }

    public function testGetAuthorityWithUserAndPassword()
    {
        $this->assertEquals($this->object->getAuthority(), 'username:password@hostname');
    }

    public function testGetAuthorityWithUserAndPasswordAndPort()
    {
        $uri = Uri::create('http://username:password@hostname:1');
        $this->assertEquals($uri->getAuthority(), 'username:password@hostname:1');
    }

    public function testUserInfo()
    {
        $uri = Uri::create('http://username@hostname');
        $this->assertEquals($uri->getUserInfo(), 'username');
    }

    public function testUserInfoWithPassword()
    {
        $this->assertEquals($this->object->getUserInfo(), 'username:password');
    }

    public function testGetHost()
    {
        $this->assertEquals($this->object->getHost(), 'hostname');

        $uri = Uri::create('http://HostName/path?arg=value#anchor');
        $this->assertEquals($uri->getHost(), 'hostname');
    }

    public function testGetPort()
    {
        $this->assertNull($this->object->getPort());
        $uri = Uri::create('http://username:password@hostname:1');
        $this->assertEquals($uri->getPort(), 1);
    }

    public function testGetPath()
    {
        $this->assertEquals($this->object->getPath(), '/path');
    }

    public function testGetQuery()
    {
        $this->assertEquals($this->object->getQuery(), 'arg=value');
    }

    public function testGetQueryVoid()
    {
        $uri = Uri::create('http://hostname:1/path');
        $this->assertEquals($uri->getQuery(), '');
    }

    public function testGetQueryMultiple()
    {
        $uri = Uri::create('http://hostname/path?arg=value&arg2=value2');
        $this->assertEquals($uri->getQuery(), 'arg=value&arg2=value2');
    }

    public function testGetQueryEncode()
    {
        $uri = Uri::create('http://hostname/path?arg=val%ue');
        $this->assertEquals($uri->getQuery(), 'arg=val%25ue');
    }

    public function testGetFragment()
    {
        $this->assertEquals($this->object->getFragment(), 'anchor');
    }

    public function testGetFragmentVoid()
    {
        $uri = Uri::create('http://hostname:1/path');
        $this->assertEquals($uri->getFragment(), '');
    }

    public function testGetFragmentEncode()
    {
        $uri = Uri::create('http://username:password@hostname:1/path#anc%hor');
        $this->assertEquals($uri->getFragment(), 'anc%25hor');
    }

    public function testWithScheme()
    {
        $uri = new Uri();
        $this->assertAttributeSame('http', 'scheme', $uri->withScheme('http'));
        $this->assertAttributeSame('http', 'scheme', $uri->withScheme('HTTP'));
    }

    /**
     * @expectedException Exception
     */
    public function testWithSchemeException()
    {
        $uri = new Uri();
        $uri->withScheme('error');
    }

    public function testWithUserInfo()
    {
        $uri = new Uri();
        $uri = $uri->withUserInfo('username');
        $this->assertAttributeSame('username', 'user', $uri);
        $this->assertAttributeSame('', 'password', $uri);
        $uri = $uri->withUserInfo('username', 'password');
        $this->assertAttributeSame('password', 'password', $uri);
    }

    /**
     * @expectedException Exception
     */
    public function testWithUserInfoException()
    {
        $uri = new Uri();
        $uri->withUserInfo(1);
    }

    public function testWithHost()
    {
        $uri = new Uri();
        $this->assertAttributeSame('hostname', 'host', $uri->withHost('hostname'));
        $this->assertAttributeSame('hostname', 'host', $uri->withHost('HostName'));
    }

    /**
     * @expectedException Exception
     */
    public function testWithHostException()
    {
        $uri = new Uri();
        $uri->withHost(1);
    }

    public function testWithPort()
    {
        $uri = new Uri();
        $this->assertAttributeSame(null, 'port', $uri->withPort('80'));
        $uri = new Uri('http');
        $this->assertAttributeSame(null, 'port', $uri->withPort('80'));
        $uri = new Uri('ftp');
        $this->assertAttributeSame(80, 'port', $uri->withPort('80'));
        $uri = new Uri('ftp');
        $this->assertAttributeSame(80, 'port', $uri->withPort(80));
    }

    /**
     * @expectedException Exception
     */
    public function testWithPortException()
    {
        $uri = new Uri();
        $uri->withPort('error');
    }

    public function testWithPath()
    {
        $uri = new Uri();
        $this->assertAttributeSame('/path', 'path', $uri->withPath('/path'));
        $this->assertAttributeSame('path', 'path', $uri->withPath('path'));
    }

    /**
     * @expectedException Exception
     */
    public function testWithPathException()
    {
        $uri = new Uri();
        $uri->withPath(1);
    }

    public function testWithQuery()
    {
        $uri = new Uri();
        $this->assertAttributeSame('arg=value', 'query', $uri->withQuery('arg=value'));
        $this->assertAttributeSame('arg=value/value2', 'query', $uri->withQuery('arg=value/value2'));
    }

    /**
     * @expectedException Exception
     */
    public function testWithQueryException()
    {
        $uri = new Uri();
        $uri->withQuery(1);
    }

    public function testWithFragment()
    {
        $uri = new Uri();
        $this->assertAttributeSame('fragment', 'fragment', $uri->withFragment('fragment'));
    }

    public function testToString()
    {
        $this->assertEquals(( string ) $this->object, 'http://username:password@hostname/path?arg=value#anchor');
        $this->assertEquals(( string ) $this->object->withPath('path'), 'http://username:password@hostname/path?arg=value#anchor');
    }
}