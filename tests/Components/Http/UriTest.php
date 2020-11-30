<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Uri;

class UriTest extends \PHPUnit\Framework\TestCase
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

    public function testCreate()
    {
        $this->assertAttributeSame('http', 'scheme', $this->object);
        $this->assertAttributeSame('username', 'user', $this->object);
        $this->assertAttributeSame('password', 'pass', $this->object);
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
        $this->assertAttributeSame('password', 'pass', $uri);
        $this->assertAttributeSame('hostname', 'host', $uri);
        $this->assertAttributeSame(null, 'port', $uri);
        $this->assertAttributeSame('/path', 'path', $uri);
        $this->assertAttributeSame('arg=value', 'query', $uri);
        $this->assertAttributeSame('anchor', 'fragment', $uri);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateUriInvalidArgument()
    {
        Uri::create('http:///www.error.org');
    }

    public function testScheme()
    {
        $this->assertSame('', Uri::create('/')->getScheme());
        $this->assertEquals('https', Uri::create('https://foo.com/')->getScheme());

        $this->assertEquals('http', Uri::create('')->withScheme('http')->getScheme());
    }

    /**
     * @dataProvider getInvalidSchemaArguments
     * @expectedException \Exception
     */
    public function testWithSchemeException($schema)
    {
        $this->object->withScheme($schema);
    }

    public function getInvalidSchemaArguments()
    {
        return [
            [ 'error' ],
            [ true ],
            [ [ 'error' ] ],
            [ 1 ],
            [ new \stdClass() ],
        ];
    }

    public function testGetAuthority()
    {
        $this->assertEquals('', Uri::create('/')->getAuthority());
        $this->assertEquals('foo@bar.com', Uri::create('http://foo@bar.com:80/')->getAuthority());
        $this->assertEquals('foo@bar.com:81', Uri::create('http://foo@bar.com:81/')->getAuthority());
        $this->assertEquals('user:foo@bar.com', Uri::create('http://user:foo@bar.com/')->getAuthority());
    }

    public function testUserInfo()
    {
        $this->assertEquals('', Uri::create('/')->getUserInfo());
        $this->assertEquals('user:foo', Uri::create('http://user:foo@bar.com/')->getUserInfo());
        $this->assertEquals('foo', Uri::create('http://foo@bar.com/')->getUserInfo());
        
        $this->assertEquals('', $this->object->withUserInfo('')->getUserInfo());
        $this->assertEquals('', $this->object->withUserInfo(null)->getUserInfo());
        $this->assertEquals('user:foo', $this->object->withUserInfo('user', 'foo')->getUserInfo());
        $this->assertEquals('foo', $this->object->withUserInfo('foo')->getUserInfo());
    }

    public function testGetHost()
    {
        $this->assertEquals($this->object->getHost(), 'hostname');

        $uri = Uri::create('http://HostName/path?arg=value#anchor');
        $this->assertEquals($uri->getHost(), 'hostname');
    }

    public function testPort()
    {
        $this->assertNull(Uri::create('http://www.foo.com/')->getPort());
        $this->assertNull(Uri::create('http://www.foo.com:80/')->getPort());
        $this->assertNull(Uri::create('https://www.foo.com:443/')->getPort());
        $this->assertSame(1, Uri::create('http://www.foo.com:1/')->getPort());

        $this->assertNull($this->object->withPort(null)->getPort());
        $this->assertNull($this->object->withPort(80)->getPort());
        $this->assertNull(Uri::create('https://www.foo.com/')->withPort(443)->getPort());
        $this->assertSame(1, $this->object->withPort(1)->getPort());
    }
    
    /**
     * @expectedException \Exception
     */
    public function testWithPortException()
    {
        $uri = new Uri();
        $uri->withPort('error');
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
     * @expectedException \Exception
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
     * @expectedException \Exception
     */
    public function testWithHostException()
    {
        $uri = new Uri();
        $uri->withHost(1);
    }

    public function testWithPath()
    {
        $uri = new Uri();
        $this->assertAttributeSame('/path', 'path', $uri->withPath('/path'));
        $this->assertAttributeSame('path', 'path', $uri->withPath('path'));
    }

    /**
     * @expectedException \Exception
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
     * @expectedException \Exception
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
        $this->assertEquals((string) $this->object, 'http://username:password@hostname/path?arg=value#anchor');
        $this->assertEquals((string) $this->object->withPath('path'), 'http://username:password@hostname/path?arg=value#anchor');
    }
}
