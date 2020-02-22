<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class ImageDimensionsHeight extends ImageDimensions
{
    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output               = parent::messages();
        $output[ 'must' ]     = 'La hauteur de l\'image :label doit être comprise entre les valeurs :minpx et :maxpx.';
        $output[ 'not_must' ] = 'La hauteur de l\'image :label ne doit pas être comprise entre les valeurs :minpx et :maxpx.';

        return $output;
    }
}
