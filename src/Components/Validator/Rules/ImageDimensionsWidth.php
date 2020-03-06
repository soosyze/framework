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
class ImageDimensionsWidth extends ImageDimensions
{
    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output               = parent::messages();
        $output[ 'must' ]     = 'The width of the :label image must be between :minpx and :maxpx.';
        $output[ 'not_must' ] = 'The width of the :label image must not be between :minpx and :maxpx.';

        return $output;
    }
}
