<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\HttpClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Soosyze\Components\Http\Response;
use Soosyze\Components\Http\Stream;
use Soosyze\Components\HttpClient\Exception\ClientException;
use Soosyze\Components\HttpClient\Exception\NetworkException;
use Soosyze\Components\HttpClient\Exception\RequestException;

/**
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Client implements ClientInterface
{
    /**
     * cUrl handle
     *
     * @var resource
     */
    protected $handle;

    /**
     * cUrl options
     *
     * @var array
     */
    protected $curlOptions;

    /**
     * @param array $curlOptions
     */
    public function __construct(array $curlOptions = [])
    {
        $this->curlOptions = $curlOptions;
    }

    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @throws ClientException If an error happens while processing the request.
     *
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->setCurlInit();
        $this->setCurlOptions($request);

        $exec  = curl_exec($this->handle);
        $errno = curl_errno($this->handle);

        switch ($errno) {
            case CURLE_OK:
                break;
            case CURLE_COULDNT_RESOLVE_PROXY:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_COULDNT_CONNECT:
            case CURLE_OPERATION_TIMEOUTED:
            case CURLE_SSL_CONNECT_ERROR:
                throw new NetworkException($request, curl_error($this->handle));
            default:
                throw new RequestException($request, curl_error($this->handle));
        }

        $code       = curl_getinfo($this->handle, CURLINFO_RESPONSE_CODE);
        $headerSize = curl_getinfo($this->handle, CURLINFO_HEADER_SIZE);
        $headers    = substr($exec, 0, $headerSize);
        $body       = substr($exec, $headerSize);

        curl_close($this->handle);
        unset($this->handle);

        $response = new Response($code, new Stream($body));

        return $this->curlParseHeaders($headers, $response);
    }

    /**
     * Parse les entêtes de la réponse.
     *
     * @param string            $headers
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function curlParseHeaders($headers, ResponseInterface $response): ResponseInterface
    {
        foreach (explode("\n", $headers) as $header) {
            $colpos = strpos($header, ':');

            if ($colpos === false || $colpos === 0) {
                continue;
            }

            [ $name, $value ] = explode(':', $header, 2);

            $response = $response->withAddedHeader(trim($name), trim($value));
        }

        return $response;
    }

    /**
     * @throws ClientException
     *
     * @return void
     */
    protected function setCurlInit(): void
    {
        $this->handle = curl_init();

        if (!is_resource($this->handle)) {
            throw new ClientException(curl_error($this->handle));
        }
    }

    /**
     * Charge la configuration de cUrl.
     *
     * @param RequestInterface $request
     *
     * @throws ClientException
     *
     * @return void
     */
    protected function setCurlOptions(RequestInterface $request): void
    {
        $this->curlOptions += [
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_CONNECTTIMEOUT  => 30,
            CURLOPT_CUSTOMREQUEST   => $request->getMethod(),
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HEADER          => true,
            CURLOPT_MAXREDIRS       => 20,
            CURLOPT_POSTFIELDS      => (string) $request->getBody(),
            CURLOPT_PROTOCOLS       => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_URL             => (string) $request->getUri()
        ];

        foreach ($request->getHeaders() as $name => $values) {
            $this->curlOptions[ CURLOPT_HTTPHEADER ][] = $name . ': ' . implode(', ', $values);
        }

        $result = curl_setopt_array($this->handle, $this->curlOptions);

        if (!$result) {
            throw new ClientException('Unable to configure cURL session');
        }
    }
}
