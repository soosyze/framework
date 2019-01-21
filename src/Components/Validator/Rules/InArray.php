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
class InArray extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est contenu dans un tableau.
     *
     * @param string $key   Clé du test.
     * @param scalar $value Valeur à tester.
     * @param array  $arg   Tableau de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        $array = explode(',', $arg);
        if (!in_array($value, $array) && $not) {
            $this->addReturn($key, 'must');
        } elseif (in_array($value, $array) && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur :label n\'est pas dans la liste.',
            'not'  => 'La valeur de :label ne doit pas être dans la liste.'
        ];
    }
}
