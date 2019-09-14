<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\HttpFactories
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\HttpFactories;

use Soosyze\Components\Http\Request;
use Soosyze\Components\Http\Uri;

/**
 * Has the ability to create client requests.
 *
 * @link https://www.php-fig.org/psr/psr-17/ PSR-17: HTTP Factories
 *
 * @author Mathieu NOËL
 */
class RequestFactory
{
    /**
     * Create a new request.
     *
     * @param string              $method The HTTP method associated with the request.
     * @param UriInterface|string $uri    The URI associated with the request.
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function createRequest($method, $uri)
    {
        $uri = \is_string($uri)
            ? Uri::create($uri)
            : $uri;

        return new Request($method, $uri);
    }
}
