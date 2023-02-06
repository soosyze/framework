<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\HttpFactories;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Soosyze\Components\Http\ServerRequest;
use Soosyze\Components\Http\Uri;

/**
 * Has the ability to create server requests.
 *
 * @link https://www.php-fig.org/psr/psr-17/ PSR-17: HTTP Factories
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * Create a new server request.
     *
     * Note that server parameters are taken precisely as given - no parsing/processing
     * of the given values is performed. In particular, no attempt is made to
     * determine the HTTP method or URI, which must be provided explicitly.
     *
     * @param string                                $method       The HTTP method associated with the request.
     * @param \Psr\Http\Message\UriInterface|string $uri          The URI associated with the request.
     * @param array                                 $serverParams An array of Server API (SAPI) parameters with
     *                                                            which to seed the generated request instance.
     */
    public function createServerRequest(
        string $method,
        $uri,
        array $serverParams = []
    ): ServerRequestInterface {
        $method = empty($method) && !empty($serverParams[ 'REQUEST_METHOD' ])
            ? $serverParams[ 'REQUEST_METHOD' ]
            : $method;
        $uri    = \is_string($uri)
            ? Uri::create($uri)
            : $uri;

        return new ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
}
