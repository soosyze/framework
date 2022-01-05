<?php

namespace Soosyze\Tests\Components\Http;

use Soosyze\Components\Http\Uri;

class UriTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Uri
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Uri('http', 'hostname', '/path', 80, 'arg=value', 'anchor', 'username', 'password');
    }

    public function testNewUri(): void
    {
        $this->assertEquals('http', $this->object->getScheme());
        $this->assertEquals('username:password', $this->object->getUserInfo());
        $this->assertEquals('hostname', $this->object->getHost());
        $this->assertEquals(null, $this->object->getPort());
        $this->assertEquals('/path', $this->object->getPath());
        $this->assertEquals('arg=value', $this->object->getQuery());
        $this->assertEquals('anchor', $this->object->getFragment());
    }

    public function testCreate(): void
    {
        $uri = Uri::create('http://username:password@hostname:80/path?arg=value#anchor');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('username:password', $uri->getUserInfo());
        $this->assertEquals('hostname', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/path', $uri->getPath());
        $this->assertEquals('arg=value', $uri->getQuery());
        $this->assertEquals('anchor', $uri->getFragment());
    }

    public function testCreateUriInvalidArgument(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Uri::create('http:///www.error.org');
    }

    public function testScheme(): void
    {
        $this->assertEquals('', Uri::create('/')->getScheme());
        $this->assertEquals('https', Uri::create('https://foo.com/')->getScheme());
        $this->assertEquals('http', Uri::create('')->withScheme('http')->getScheme());
    }

    /**
     * @dataProvider providerWithSchemeInvalidArgumentException
     *
     * @param mixed $schema
     */
    public function testWithSchemeInvalidArgumentException($schema): void
    {
        $this->expectException(\InvalidArgumentException::class);
        /** @phpstan-ignore-next-line */
        $this->object->withScheme($schema);
    }

    public function providerWithSchemeInvalidArgumentException(): \Generator
    {
        yield [ [ 'error' ] ];
        yield [ true ];
        yield [ 1.1 ];
        yield [ 1 ];
        yield [ new \stdClass() ];
        yield [ 'error' ];
    }

    /**
     * @dataProvider providerGetAuthority
     */
    public function testGetAuthority(string $expectedAuthority, string $uri): void
    {
        $this->assertEquals($expectedAuthority, Uri::create($uri)->getAuthority());
    }

    public function providerGetAuthority(): \Generator
    {
        yield [ '', '/' ];
        yield [ 'foo@bar.com', 'http://foo@bar.com:80/' ];
        yield [ 'foo@bar.com:81', 'http://foo@bar.com:81/' ];
        yield [ 'user:foo@bar.com', 'http://user:foo@bar.com/' ];
    }

    /**
     * @dataProvider providerGetQuery
     */
    public function testGetQuery(string $expectedQuery, string $uri): void
    {
        $this->assertEquals($expectedQuery, Uri::create($uri)->getQuery());
    }

    public function providerGetQuery(): \Generator
    {
        yield [ '', 'http://hostname:1/path' ];
        yield [ 'arg=value&arg2=value2', 'http://hostname/path?arg=value&arg2=value2' ];
        yield [ 'arg=val%25ue', 'http://hostname/path?arg=val%ue' ];
    }

    public function testUserInfo(): void
    {
        $this->assertEquals('', Uri::create('/')->getUserInfo());
        $this->assertEquals('user:foo', Uri::create('http://user:foo@bar.com/')->getUserInfo());
        $this->assertEquals('foo', Uri::create('http://foo@bar.com/')->getUserInfo());

        $this->assertEquals('', $this->object->withUserInfo('')->getUserInfo());
        /** @phpstan-ignore-next-line */
        $this->assertEquals('', $this->object->withUserInfo(null)->getUserInfo());
        $this->assertEquals('user:foo', $this->object->withUserInfo('user', 'foo')->getUserInfo());
        $this->assertEquals('foo', $this->object->withUserInfo('foo')->getUserInfo());
    }

    public function testGetHost(): void
    {
        $this->assertEquals('hostname', $this->object->getHost());

        $uri = Uri::create('http://HostName/path?arg=value#anchor');
        $this->assertEquals('hostname', $uri->getHost());
    }

    public function testPort(): void
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

    public function testWithPortException(): void
    {
        $uri = new Uri();

        $this->expectException(\Exception::class);
        /** @phpstan-ignore-next-line */
        $uri->withPort('error');
    }

    public function testGetFragmentVoid(): void
    {
        $uri = Uri::create('http://hostname:1/path');
        $this->assertEquals('', $uri->getFragment());
    }

    public function testGetFragmentEncode(): void
    {
        $uri = Uri::create('http://username:password@hostname:1/path#anc%hor');
        $this->assertEquals('anc%25hor', $uri->getFragment());
    }

    public function testWithScheme(): void
    {
        $uri = new Uri();
        $this->assertEquals('http', $uri->withScheme('http')->getScheme());
        $this->assertEquals('http', $uri->withScheme('HTTP')->getScheme());
    }

    public function testWithUserInfoException(): void
    {
        $uri = new Uri();

        $this->expectException(\Exception::class);
        /** @phpstan-ignore-next-line */
        $uri->withUserInfo(1);
    }

    public function testWithHost(): void
    {
        $uri = new Uri();
        $this->assertEquals('hostname', $uri->withHost('hostname')->getHost());
        $this->assertEquals('hostname', $uri->withHost('HostName')->getHost());
    }

    public function testWithHostException(): void
    {
        $uri = new Uri();

        $this->expectException(\Exception::class);
        /** @phpstan-ignore-next-line */
        $uri->withHost(1);
    }

    public function testWithPath(): void
    {
        $uri = new Uri();
        $this->assertEquals('/path', $uri->withPath('/path')->getPath());
        $this->assertEquals('path', $uri->withPath('path')->getPath());
    }

    public function testWithPathException(): void
    {
        $uri = new Uri();

        $this->expectException(\Exception::class);
        /** @phpstan-ignore-next-line */
        $uri->withPath(1);
    }

    public function testWithQuery(): void
    {
        $uri = new Uri();
        $this->assertEquals('arg=value', $uri->withQuery('arg=value')->getQuery());
        $this->assertEquals('arg=value/value2', $uri->withQuery('arg=value/value2')->getQuery());
    }

    public function testWithQueryException(): void
    {
        $uri = new Uri();

        $this->expectException(\Exception::class);
        /** @phpstan-ignore-next-line */
        $uri->withQuery(1);
    }

    public function testWithFragment(): void
    {
        $uri = new Uri();
        $this->assertEquals('fragment', $uri->withFragment('fragment')->getFragment());
    }

    public function testToString(): void
    {
        $this->assertEquals('http://username:password@hostname/path?arg=value#anchor', (string) $this->object);
        $this->assertEquals('http://username:password@hostname/other/path?arg=value#anchor', (string) $this->object->withPath('other/path'));
    }
}
