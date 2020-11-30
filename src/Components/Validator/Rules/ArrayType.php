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
class ArrayType extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si la valeur est de type array.
     *
     * @param string $key    Clé du test.
     * @param mixed  $values Valeur à tester.
     * @param string $arg    Argument de test.
     * @param bool   $not    Inverse le test.
     */
    protected function test($key, $values, $arg, $not)
    {
        if (!\is_array($values) && $not) {
            $this->addReturn($key, 'must');
        } elseif (\is_array($values) && !$not) {
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
            'must' => 'The value of the :label field must be an array.',
            'not'  => 'The value of the :label field must not be an array.'
        ];
    }
}
