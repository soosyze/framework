<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\HttpFactories
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\HttpFactories;

use Soosyze\Components\Http\Uri;

/**
 * Has the ability to create URIs for client and server requests.
 *
 * @link https://www.php-fig.org/psr/psr-17/ PSR-17: HTTP Factories
 *
 * @author Mathieu NOËL
 */
class UriFactory
{
    /**
     * Create a new URI.
     *
     * @param string $uri The URI to parse.
     *
     * @throws \InvalidArgumentException      If the given URI cannot be parsed.
     * @return \Psr\Http\Message\UriInterface
     */
    public function createUri($uri = '')
    {
        return Uri::create($uri);
    }
}
