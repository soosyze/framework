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
class IntType extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est de type entier.
     *
     * @param string $key   Clé du test.
     * @param mixed  $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        $filter = filter_var($value, FILTER_VALIDATE_INT);
        if (!is_int($filter) && $not) {
            $this->addReturn($key, 'must');
        } elseif (is_int($filter) && !$not) {
            $this->addReturn($key, 'not');
        }

        if ($this->hasErrors()) {
            $this->stopPropagation();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'The value of the :label field must be an integer.',
            'not'  => 'The value of the :label field must not be an integer.'
        ];
    }
}
