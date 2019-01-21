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
class ImageDimensionsHeight extends Image
{
    /**
     * Test la hauteur d'une image.
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
        $this->sizeBetween($length[ 'height' ], $between[ 'min' ], $between[ 'max' ], $not);
    }

    /**
     * Test la hauteur d'une image.
     *
     * @param int     $lengthValue Hauteur de l'image en pixel.
     * @param numeric $min         Hauteur minimum autorisée.
     * @param numeric $max         Hauteur maximum autorisée.
     * @param bool    $not         Inverse le test.
     */
    protected function sizeBetween($lengthValue, $min, $max, $not = true)
    {
        if (!($lengthValue <= $max && $lengthValue >= $min) && $not) {
            $this->addReturn('image_dimensions_height', 'must', [ ':min' => $min,
                ':max' => $max ]);
        } elseif ($lengthValue <= $max && $lengthValue >= $min && !$not) {
            $this->addReturn('image_dimensions_height', 'not', [ ':min' => $min,
                ':max' => $max ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output           = parent::messages();
        $output[ 'must' ] = 'La hauteur de l\'image :label doit être comprise entre les valeurs :minpx et :maxpx.';
        $output[ 'not' ]  = 'La hauteur de l\'image :label ne doit pas être comprise entre les valeurs :minpx et :maxpx.';

        return $output;
    }
}
