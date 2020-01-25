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
class AlphaNum extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si la valeur est Alpha numérique [a-zA-Z0-9].
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        if (!ctype_alnum($value) && $not) {
            $this->addReturn('alpha_num', 'must');
        } elseif (ctype_alnum($value) && !$not) {
            $this->addReturn('alpha_num', 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label n\'est pas alpha numérique.',
            'not'  => 'La valeur de :label ne doit pas être alpha numérique.'
        ];
    }
}
