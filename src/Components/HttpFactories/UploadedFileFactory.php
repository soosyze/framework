<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\HttpFactories;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Soosyze\Components\Http\UploadedFile;

/**
 * Has the ability to create streams for uploaded files.
 *
 * @link https://www.php-fig.org/psr/psr-17/ PSR-17: HTTP Factories
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class UploadedFileFactory
{
    /**
     * Create a new uploaded file.
     *
     * If a size is not provided it will be determined by checking the size of
     * the stream.
     *
     * @link http://php.net/manual/features.file-upload.post-method.php
     * @link http://php.net/manual/features.file-upload.errors.php
     *
     * @param StreamInterface $stream          The underlying stream representing the
     *                                         uploaded file content.
     * @param int             $size            The size of the file in bytes.
     * @param int             $error           The PHP file upload error.
     * @param string          $clientFilename  The filename as provided by the client, if any.
     * @param string          $clientMediaType The media type as provided by the client, if any.
     *
     * @throws \InvalidArgumentException If the file resource is not readable.
     *
     * @return UploadedFileInterface
     */
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        return new UploadedFile($stream, $clientFilename, $size, $clientMediaType, $error);
    }
}
