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
class Uuid extends \Soosyze\Components\Validator\Rule
{
    const UUID_V4 = '/^[\da-z]{8}-[\da-z]{4}-4[\da-z]{3}-[89ab][\da-z]{3}-[\da-z]{12}$/i';

    /**
     * Test si la valeur est égale à "1", "true", "on" et "yes".
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        if (!preg_match(self::UUID_V4, $value) && $not) {
            $this->addReturn($key, 'must', [ ':regex' => $arg ]);
        } elseif (preg_match(self::UUID_V4, $value) && !$not) {
            $this->addReturn($key, 'not', [ ':regex' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'Le champ :label doit être accepté.',
            'not'  => 'Le champ :label ne doit pas être accepté.'
        ];
    }
}
