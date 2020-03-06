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
class Email extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est un email.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL) && $not) {
            $this->addReturn($key, 'must');
        } elseif (filter_var($value, FILTER_VALIDATE_EMAIL) && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'The :label field must be a valid email address.',
            'not'  => 'The :label field must not be a valid email address.'
        ];
    }
}
