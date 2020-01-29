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
class BoolType extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est de type boolean.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        if (!$this->isBool($value) && $not) {
            $this->addReturn($key, 'must');
        } elseif ($this->isBool($value) && !$not) {
            $this->addReturn($key, 'not');
        }

        if ($this->hasErrors()) {
            $this->stopPropagation();
        }
    }

    /**
     * Si la variable contient une valeur boolean.
     *
     * @param mixed $var Variable testée.
     *
     * @return bool
     */
    protected function isBool($var)
    {
        return filter_var($var, FILTER_VALIDATE_BOOLEAN)
            || filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null
            || $var === false
            || $var === '';
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label n\'est pas un boolean.',
            'not'  => 'La valeur de :label ne doit pas être un boolean.'
        ];
    }
}
