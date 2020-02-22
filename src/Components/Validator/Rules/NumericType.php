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
class NumericType extends \Soosyze\Components\Validator\Rule
{
    protected function test($key, $value, $args, $not)
    {
        if (!is_numeric($value) && $not) {
            $this->addReturn($key, 'must');
        } elseif (is_numeric($value) && !$not) {
            $this->addReturn($key, 'not');
        }

        if ($this->hasErrors()) {
            $this->stopPropagation();
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
