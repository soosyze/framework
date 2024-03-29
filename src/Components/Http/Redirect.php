<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Http;

use Psr\Http\Message\StreamInterface;

/**
 * Représentation d'une réponse sortante côté serveur provoquant une redirection.
 *
 * @link https://www.php-fig.org/psr/psr-7/ PSR-7: HTTP message interfaces
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Redirect extends Response
{
    /**
     * Codes de redirection valident.
     *
     * @var int[]
     */
    protected $codeRedirectValid = [
        300, 301, 302, 303, 304, 305, 306, 307, 308, 310
    ];

    /**
     * Construit une réponse de redirection.
     *
     * @param string                            $location Url de redirection.
     * @param int                               $code     Code de redirection.
     * @param \Psr\Http\Message\StreamInterface $body     Corp du message.
     * @param array                             $headers  En-tête de la réponse.
     */
    public function __construct(
        string $location,
        int $code = 301,
        ?StreamInterface $body = null,
        array $headers = []
    ) {
        parent::__construct($code, $body, $headers);
        $this->headers[ 'location' ] = [ $location ];
    }

    /**
     * Redéfini `filtreCode()` afin que le code soit valide pour une redirection.
     *
     * @param int $code Le code d'état filtré.
     *
     * @throws \InvalidArgumentException Status code is invalid for redirect.
     *
     * @return int
     */
    protected function filtreCode($code = 301): int
    {
        $codeRedirectValid = \array_fill_keys($this->codeRedirectValid, true);

        if (!isset($codeRedirectValid[ $code ])) {
            throw new \InvalidArgumentException('Status code is invalid for redirect.');
        }

        return $code;
    }
}
