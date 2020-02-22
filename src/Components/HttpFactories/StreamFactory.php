<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\HttpFactories;

use Soosyze\Components\Http\Stream;

/**
 * Has the ability to create streams for requests and responses.
 *
 * @link https://www.php-fig.org/psr/psr-17/ PSR-17: HTTP Factories
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class StreamFactory
{
    /**
     * Create a new stream from a string.
     *
     * The stream SHOULD be created with a temporary resource.
     *
     * @param string $content String content with which to populate the stream.
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function createStream($content = '')
    {
        return new Stream($content);
    }

    /**
     * Create a stream from an existing file.
     *
     * The file MUST be opened using the given mode, which may be any mode
     * supported by the `fopen` function.
     *
     * The `$filename` MAY be any string supported by `fopen()`.
     *
     * @param string $filename The filename or stream URI to use as basis of stream.
     * @param string $mode     The mode with which to open the underlying filename/stream.
     *
     * @throws \RuntimeException                 If the file cannot be opened.
     * @throws \InvalidArgumentException         If the mode is invalid.
     * @return \Psr\Http\Message\StreamInterface
     */
    public function createStreamFromFile($filename, $mode = 'r')
    {
        return Stream::createStreamFromFile($filename, $mode);
    }

    /**
     * Create a new stream from an existing resource.
     *
     * The stream MUST be readable and may be writable.
     *
     * @param resource $resource The PHP resource to use as the basis for the stream.
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function createStreamFromResource($resource)
    {
        return new Stream($resource);
    }
}
