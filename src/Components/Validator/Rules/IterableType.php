<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

if (!function_exists('is_iterable')) {
    /**
     * Détermine si le contenu de la variable est itérable pour les versions PHP5
     *
     * @param mixed $obj
     *
     * @return bool
     */
    function is_iterable($obj)
    {
        return is_array($obj) || (is_object($obj) && ($obj instanceof \Traversable));
    }
}

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class IterableType extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si la valeur est itérable.
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
            'must' => 'The value of the :label field must be iterable.',
            'not'  => 'The value of the :label field must not be iterable.'
        ];
    }
}
