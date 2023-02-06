<?php

declare(strict_types=1);

namespace Soosyze\Components\HttpClient\Exception;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Soosyze\Components\HttpClient\Exception\ClientException;

/**
 * Exception for when a request failed.
 *
 * Examples:
 *      - Request is invalid (e.g. method is missing)
 *      - Runtime request errors (e.g. the body stream is not seekable)
 */
class RequestException extends ClientException implements RequestExceptionInterface
{
    /**
     * Request instance
     *
     * @var RequestInterface
     */
    protected $request;

    public function __construct(
        RequestInterface $request,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $this->request = $request;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the request.
     *
     * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
