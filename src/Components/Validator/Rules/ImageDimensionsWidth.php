<?php

/**
 * Soosyze Framework https://soosyze.com
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
class ImageDimensionsWidth extends ImageDimensions
{
    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output                = parent::messages();
        $output[ 'must' ]     = 'La largeur de l\'image :label doit être comprise entre les valeurs :minpx et :maxpx.';
        $output[ 'not_must' ] = 'La largeur de l\'image :label ne doit pas être comprise entre les valeurs :minpx et :maxpx.';

        return $output;
    }
}
