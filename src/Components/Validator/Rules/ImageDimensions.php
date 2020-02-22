<?php

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
abstract class ImageDimensions extends FileMimetypes
{
    /**
     * {@inheritdoc}
     *
     * @param string                                  $key   Clé du test.
     * @param \Psr\Http\Message\UploadedFileInterface $value Valeur à tester.
     * @param string                                  $arg   Argument de test.
     * @param bool                                    $not   Inverse le test.
     */
    public function test($key, $value, $arg, $not)
    {
        parent::test('file_mimetypes', $value, 'image', true);

        if ($this->hasErrors()) {
            return 1;
        }

        $between = $this->getParamMinMax($arg);

        $length = $this->getDimensions($value);
        $type   = $key === 'image_dimensions_height'
            ? 'height'
            : 'width';
        $this->sizeBetween($key, $length[ $type ], $between[ 'min' ], $between[ 'max' ], $not);
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
     * Test la taille d'une image.
     *
     * @param string  $key         Clé du test.
     * @param int     $lengthValue Hauteur de l'image en pixel.
     * @param numeric $min         Hauteur minimum autorisée.
     * @param numeric $max         Hauteur maximum autorisée.
     * @param bool    $not         Inverse le test.
     */
    protected function sizeBetween($key, $lengthValue, $min, $max, $not)
    {
        if (!($lengthValue <= $max && $lengthValue >= $min) && $not) {
            $this->addReturn($key, 'must', [
                ':min' => $min,
                ':max' => $max
            ]);
        } elseif ($lengthValue <= $max && $lengthValue >= $min && !$not) {
            $this->addReturn($key, 'not_must', [
                ':min' => $min,
                ':max' => $max
            ]);
        }
    }
}
