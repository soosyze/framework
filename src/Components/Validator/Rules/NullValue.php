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
class NullValue extends \Soosyze\Components\Validator\Rule
{
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
        if ($value !== null && $not) {
            $this->addReturn($key, 'must');
        } elseif ($value === null && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'The value of the :label field must be NULL.',
            'not'  => 'The value of the :label field must not be NULL.'
        ];
    }
}
