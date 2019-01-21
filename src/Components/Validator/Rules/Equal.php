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
class Equal extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si 2 valeurs sont identiques.
     *
     * @param string $key   Clé du test.
     * @param scalar $value Valeur à tester.
     * @param scalar $arg   Valeur de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        if ($value !== $arg && $not) {
            $this->addReturn($key, 'must');
        } elseif ($value === $arg && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label n\'est pas valide.',
            'not'  => 'La valeur de :label n\'est pas valide.'
        ];
    }
}
