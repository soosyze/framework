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
class Instance extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si la valeur est de type array.
     *
     * @param string $key    Clé du test.
     * @param string $values Valeur à tester.
     * @param string $arg    Argument de test.
     * @param bool   $not    Inverse le test.
     */
    protected function test($key, $values, $arg, $not)
    {
        if (!($values instanceof $arg) && $not) {
            $this->addReturn($key, 'must');
        } elseif ($values instanceof $arg && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'Le champ :label doit être une instance de :class.',
            'not'  => 'Le champ :label ne doit pas être une instance de :class.',
        ];
    }
}
