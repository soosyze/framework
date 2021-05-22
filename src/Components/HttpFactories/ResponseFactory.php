<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\HttpFactories;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Soosyze\Components\Http\Response;

/**
 * Has the ability to create responses.
 *
 * @link https://www.php-fig.org/psr/psr-17/ PSR-17: HTTP Factories
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * Create a new response.
     *
     * @param int    $code         The HTTP status code. Defaults to 200.
     * @param string $reasonPhrase The reason phrase to associate with the status code
     *                             in the generated response. If none is provided, implementations MAY use
     *                             the defaults as suggested in the HTTP specification.
     *
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response($code, null, [], $reasonPhrase);
    }
}
