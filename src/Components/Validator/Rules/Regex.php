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
class Regex extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est égale à une expression régulière.
     *
     * @param string $key   Clé du test.
     * @param scalar $value Valeur à tester.
     * @param string $arg   Expression régulière.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        if (!preg_match($arg, $value) && $not) {
            $this->addReturn($key, 'must', [ ':regex' => $arg ]);
        } elseif (preg_match($arg, $value) && !$not) {
            $this->addReturn($key, 'not', [ ':regex' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label ne correspond pas à la règle de validation :regex.',
            'not'  => 'La valeur de :label ne doit pas correspondre à la règle de validation :regex.'
        ];
    }
}
