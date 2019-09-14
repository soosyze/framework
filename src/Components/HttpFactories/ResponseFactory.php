<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\HttpFactories
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\HttpFactories;

use Soosyze\Components\Http\Response;

/**
 * Has the ability to create responses.
 *
 * @link https://www.php-fig.org/psr/psr-17/ PSR-17: HTTP Factories
 *
 * @author Mathieu NOËL
 */
class ResponseFactory
{
    /**
     * Create a new response.
     *
     * @param int    $code         The HTTP status code. Defaults to 200.
     * @param string $reasonPhrase The reason phrase to associate with the status code
     *                             in the generated response. If none is provided, implementations MAY use
     *                             the defaults as suggested in the HTTP specification.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createResponse($code = 200, $reasonPhrase = '')
    {
        return new Response($code, null, [], $reasonPhrase);
    }
}
