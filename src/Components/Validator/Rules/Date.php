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
class Date extends \Soosyze\Components\Validator\Rule
{

    /**
     * Test si une valeur est une date.
     *
     * @param string $key Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg Argument de test.
     * @param bool $not Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        if (!strtotime($value) && $not) {
            $this->addReturn($key, 'must');
        } elseif (strtotime($value) && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label n\'est pas une date.',
            'not'  => 'La valeur de :label ne doit pas être une date.'
        ];
    }
}