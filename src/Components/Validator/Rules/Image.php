<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Psr\Http\Message\UploadedFileInterface;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
 */
class Image extends FileMimes
{

    /**
     * Test si un fichier est une image.
     *
     * @param string $key Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param string|bool $arg Liste d'extensions d'images autorisées.
     * @param bool $not Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        $extensions = $arg === false
            ? 'jpe,jpg,jpeg,png,gif,svg'
            : $arg;
        parent::test('file_mimes', $value, $extensions, $not);
        $this->validMimeImageByExtension($extensions);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output = parent::messages();

        return $output;
    }

    /**
     * Retourne les dimensions d'une image.
     *
     * @param UploadedFileInterface $upload Image
     *
     * @return int[] Dimensions
     */
    protected function getDimensions(UploadedFileInterface $upload)
    {
        $dimension = getimagesize($upload->getStream()->getMetadata('uri'));

        return [
            'width'  => $dimension[ 0 ],
            'height' => $dimension[ 1 ]
        ];
    }

    /**
     * Valide si une liste d'extensions correspond à un mimetype d'image.
     *
     * @param string $extensions Liste d'extensions d'images autorisées.
     *
     * @return bool
     */
    protected function validMimeImageByExtension($extensions)
    {
        foreach (explode(',', $extensions) as $ext) {
            if (($mimes = $this->getMimeByExtension($ext)) === false) {
                return false;
            }
            $this->validMimeImage($ext, $mimes);
        }
    }

    /**
     * Valide si un mimetype est celui d'une image.
     *
     * @param string $extension
     * @param string|array $mimes Mimetype ou liste de mimetype.
     *
     * @throws \InvalidArgumentException L'extension n'est pas une extension d'image.
     */
    private function validMimeImage($extension, $mimes)
    {
        if (is_array($mimes)) {
            foreach ($mimes as $mime) {
                if (!strstr($mime, 'image/')) {
                    throw new \InvalidArgumentException(htmlspecialchars(
                        "The extension $extension is not an image extension."
                    ));
                }
            }
        } else {
            if (!strstr($mimes, 'image/')) {
                throw new \InvalidArgumentException(htmlspecialchars(
                    "The extension $extension is not an image extension."
                ));
            }
        }
    }
}
