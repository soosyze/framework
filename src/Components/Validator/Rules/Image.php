<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Psr\Http\Message\UploadedFileInterface;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Image extends FileMimes
{
    /**
     * Test si un fichier est une image.
     *
     * @param string                $key   Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param mixed                 $args  Liste d'extensions d'images autorisées.
     * @param bool                  $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        if (!is_string($args)) {
            throw new \TypeError('The argument must be a string.');
        }
        $extensions = $args === '' ? 'jpe,jpg,jpeg,png,gif,svg' : $args;
        parent::test('file_mimes', $value, $extensions, $not);

        if ($this->hasErrors()) {
            return;
        }

        $this->validMimeImageByExtension($extensions);
    }

    /**
     * Valide si une liste d'extensions correspond à un mimetype d'image.
     *
     * @param string $extensions Liste d'extensions d'images autorisées.
     */
    protected function validMimeImageByExtension(string $extensions): void
    {
        foreach (explode(',', $extensions) as $ext) {
            $mimes = $this->getMimeByExtension($ext);
            if ($mimes === null) {
                return;
            }
            $this->validMimeImage($ext, $mimes);
        }
    }

    /**
     * Valide si un mimetype est celui d'une image.
     *
     * @param  string|array              $mimes Mimetype ou liste de mimetype.
     * @throws \InvalidArgumentException L'extension n'est pas une extension d'image.
     */
    private function validMimeImage(string $extension, $mimes): void
    {
        if (is_array($mimes)) {
            foreach ($mimes as $mime) {
                if (!strstr($mime, 'image/')) {
                    throw new \InvalidArgumentException(htmlspecialchars(
                        "The extension $extension is not an image extension."
                    ));
                }
            }
        } elseif (!strstr($mimes, 'image/')) {
            throw new \InvalidArgumentException(htmlspecialchars(
                "The extension $extension is not an image extension."
            ));
        }
    }
}
