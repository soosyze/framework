<?php

namespace Soosyze\Components\HttpClient\Exception;

use Psr\Http\Message\RequestInterface;

use Soosyze\Components\HttpClient\Exception\ClientException;

/**
 * Thrown when the request cannot be completed because of network issues.
 *
 * There is no response object as this exception is thrown when no response has been received.
 *
 * Example: the target host name can not be resolved or the connection failed.
 */
class NetworkException extends ClientException
{
    /**
     * Request instance
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * Constructor of the class
     *
     * @param RequestInterface $request
     * @param string           $message
     * @param int              $code
     * @param \Throwable|null  $previous
     */
    public function __construct(
        RequestInterface $request,
        $message = '',
        $code = 0,
        $previous = null
    ) {
        $this->request = $request;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the request.
     *
     * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
}
