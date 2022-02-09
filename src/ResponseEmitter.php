<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use Psr\Http\Message\ResponseInterface;

/**
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class ResponseEmitter
{
    /**
     * Renvoie les informations de la réponse et son contenu.
     *
     * @return string
     */
    public function emit(ResponseInterface $response): string
    {
        if (!headers_sent()) {
            $statusLine = sprintf(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            );
            header($statusLine, true, $response->getStatusCode());

            foreach ($response->getHeaders() as $name => $values) {
                $first = strtolower($name) !== 'set-cookie';
                foreach ($values as $value) {
                    $header = sprintf('%s: %s', $name, $value);
                    header($header, $first);
                    $first  = false;
                }
            }
        }

        return (string) $response->getBody();
    }
}
