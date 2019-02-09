<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
 */
class ImageDimensionsWidth extends Image
{
    /**
     * Test la largeur d'une image.
     *
     * @param string                $key   Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param string                $arg   Argument de test.
     * @param bool                  $not   Inverse le test.
     *
     * @return int 1 erreur de fichier.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        parent::test('image', $value, false);

        if ($this->hasErrors()) {
            return 1;
        }

        $between = $this->getParamMinMax($arg);

        $length = $this->getDimensions($value);
        $this->sizeBetween($length[ 'width' ], $between[ 'min' ], $between[ 'max' ], $not);
    }

    /**
     * Test la largeur d'une image.
     *
     * @param int     $lengthValue Largeur de l'image en pixel.
     * @param numeric $min         Largeur minimum autorisée.
     * @param numeric $max         Largeur maximum autorisée.
     * @param bool    $not         Inverse le test.
     */
    protected function sizeBetween($lengthValue, $min, $max, $not = true)
    {
        if (!($lengthValue <= $max && $lengthValue >= $min) && $not) {
            $this->addReturn('image_dimensions_width', 'width', [
                ':min' => $min,
                ':max' => $max
            ]);
        } elseif ($lengthValue <= $max && $lengthValue >= $min && !$not) {
            $this->addReturn('image_dimensions_width', 'not_width', [
                ':min' => $min,
                ':max' => $max
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output                = parent::messages();
        $output[ 'width' ]     = 'La largeur de l\'image :label doit être comprise entre les valeurs :minpx et :maxpx.';
        $output[ 'not_width' ] = 'La largeur de l\'image :label ne doit pas être comprise entre les valeurs :minpx et :maxpx.';

        return $output;
    }
}
