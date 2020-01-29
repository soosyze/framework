<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

if (!function_exists('is_iterable')) {
    function is_iterable($obj)
    {
        return is_array($obj) || (is_object($obj) && ($obj instanceof \Traversable));
    }
}

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
 */
class IterableType extends \Soosyze\Components\Validator\Rule
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
        if (!is_iterable($values) && $not) {
            $this->addReturn($key, 'must');
        } elseif (is_iterable($values) && !$not) {
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
            'must' => 'La valeur de :label n\'est pas un array.',
            'not'  => 'La valeur de :label ne doit pas être un array.'
        ];
    }
}
