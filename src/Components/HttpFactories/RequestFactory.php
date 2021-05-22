<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\HttpFactories;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Soosyze\Components\Http\Request;
use Soosyze\Components\Http\Uri;

/**
 * Has the ability to create client requests.
 *
 * @link https://www.php-fig.org/psr/psr-17/ PSR-17: HTTP Factories
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class RequestFactory implements RequestFactoryInterface
{
    /**
     * Create a new request.
     *
     * @param string                                $method The HTTP method associated with the request.
     * @param \Psr\Http\Message\UriInterface|string $uri    The URI associated with the request.
     *
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        $uri = \is_string($uri)
            ? Uri::create($uri)
            : $uri;

        return new Request($method, $uri);
    }
}
