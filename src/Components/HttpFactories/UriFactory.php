<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\HttpFactories;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Soosyze\Components\Http\Uri;

/**
 * Has the ability to create URIs for client and server requests.
 *
 * @link https://www.php-fig.org/psr/psr-17/ PSR-17: HTTP Factories
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class UriFactory implements UriFactoryInterface
{
    /**
     * Create a new URI.
     *
     * @param string $uri The URI to parse.
     *
     * @throws \InvalidArgumentException If the given URI cannot be parsed.
     *
     * @return UriInterface
     */
    public function createUri(string $uri = ''): UriInterface
    {
        return Uri::create($uri);
    }
}
