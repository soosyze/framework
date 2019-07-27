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
abstract class ImageDimensions extends FileMimetypes
{
    public function test($key, $value, $arg, $not = true){
        parent::test('file_mimetypes', $value, 'image');

        if ($this->hasErrors()) {
            return 1;
        }

        $between = $this->getParamMinMax($arg);

        $length = $this->getDimensions($value);
        $type   = $key === 'image_dimensions_height' ? 'height' : 'width';
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
     * @param int     $lengthValue Hauteur de l'image en pixel.
     * @param numeric $min         Hauteur minimum autorisée.
     * @param numeric $max         Hauteur maximum autorisée.
     * @param bool    $not         Inverse le test.
     */
    protected function sizeBetween($key, $lengthValue, $min, $max, $not = true)
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