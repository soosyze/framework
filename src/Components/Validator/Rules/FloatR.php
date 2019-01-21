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
class FloatR extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est de type numérique flottant.
     *
     * @param string $key   Clé du test.
     * @param float  $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        if (!is_float($value) && $not) {
            $this->addReturn($key, 'must');
        } elseif (is_float($value) && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label n\'est pas un nombre flottant.',
            'not'  => 'La valeur de :label ne doit être un nombre flottant.'
        ];
    }
}
