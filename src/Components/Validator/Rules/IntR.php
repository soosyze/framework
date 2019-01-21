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
class IntR extends \Soosyze\Components\Validator\Rule
{

    /**
     * Test si une valeur est de type entier.
     *
     * @param string $key Clé du test.
     * @param int $value Valeur à tester.
     * @param string $arg Argument de test.
     * @param bool $not Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        if (!filter_var($value, FILTER_VALIDATE_INT) && $not) {
            $this->addReturn($key, 'must');
        } elseif (filter_var($value, FILTER_VALIDATE_INT) && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label n\'est pas un nombre entier.',
            'not'  => 'La valeur de :label ne doit être un nombre entier.'
        ];
    }
}