<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Http
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Http;

use \Psr\Http\Message\ResponseInterface;

/**
 * Représentation d'une réponse sortante côté serveur.
 *
 * @link https://www.php-fig.org/psr/psr-7/ PSR-7: HTTP message interfaces
 *
 * @author Mathieu NOËL
 */
class Reponse extends Message implements ResponseInterface
{
    /**
     * Code d'état.
     *
     * @var int
     */
    protected $code;

    /**
     * Phrase de raison.
     *
     * @var string
     */
    protected $reasonPhrase;

    /**
     * Code d'état et phrases de raison autorisés.
     *
     * @var array
     */
    protected $reasonPhraseDefault = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        598 => 'Network read timeout error',
    ];

    /**
     * Construit une réponse à partir de son code, son message et de ses en-têtes.
     *
     * @param int $code Code d'état.
     * @param \Psr\Http\Message\StreamInterface $body Corp de la réponse.
     * @param array $headers En-tête de la réponse.
     */
    public function __construct(
        $code = 200,
        \Psr\Http\Message\StreamInterface $body = null,
        array $headers = []
    ) {
        $this->code         = $this->filtreCode($code);
        $this->reasonPhrase = $this->reasonPhraseDefault[ $this->code ];
        $this->body         = $body;
        $this->headers      = $headers;
    }

    /**
     * Renvoie les informations de la réponse et son contenu.
     *
     * @return string
     */
    public function __toString()
    {
        header('HTTP/' . $this->protocolVersion . ' ' . $this->code . ' ' . $this->reasonPhrase, true);

        return $this->getBody()->__toString();
    }

    /**
     * Obtient le code d'état de la réponse.
     *
     * @return int Code d'état.
     */
    public function getStatusCode()
    {
        return $this->code;
    }

    /**
     * Renvoie une instance avec le code d'état spécifié et, éventuellement, une phrase de raison.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @param int $code Code de résultat de l'entier à 3 chiffres à définir.
     * @param string $reasonPhrase La phrase de raison à utiliser avec le
     * fourni le code d'état; si aucune n'est fournie, les mises en œuvre PEUVENT
     * utiliser les valeurs par défaut comme suggéré dans la spécification HTTP.
     *
     * @return static
     *
     * @throws \InvalidArgumentException Pour les arguments de code d'état non valides.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $clone       = clone $this;
        $clone->code = $this->filtreCode($code);
        if (!is_string($reasonPhrase)) {
            throw new \InvalidArgumentException('The phrase of reason must be a string.');
        }
        $clone->reasonPhrase = $reasonPhrase === ''
            ? $this->reasonPhraseDefault[ $code ]
            : $reasonPhrase;

        return $clone;
    }

    /**
     * Obtient la phrase de raison de la réponse.
     *
     * @return int Code d'état.
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * Filtre le code d'état.
     *
     * @param int $code Code d'état.
     *
     * @return int Le code d'état filtré.
     *
     * @throws \InvalidArgumentException Le code de statut n'est pas valide.
     */
    protected function filtreCode($code)
    {
        if (is_numeric($code)) {
            $code = ( int ) $code;
        }
        if (!isset($this->reasonPhraseDefault[ $code ])) {
            throw new \InvalidArgumentException('Status code is invalid.');
        }

        return $code;
    }
}
