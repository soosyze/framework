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
class NumericType extends \Soosyze\Components\Validator\Rule
{
    protected function test($key, $value, $args, $not = true)
    {
        if (!is_numeric($value) && $not) {
            $this->addReturn($key, 'must');
        } elseif (is_numeric($value) && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    protected function messages()
    {
        return [
            'must' => 'La valeur de :label doit être une valeur numérique.',
            'not'  => 'La valeur de :label ne doit pas être une valeur numérique.'
        ];
    }
}
