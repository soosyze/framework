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
class BoolType extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est de type boolean.
     *
     * @param string $key   Clé du test.
     * @param mixed  $value Valeur à tester.
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
     * @see https://www.php.net/ChangeLog-5.php#PHP_5_4 (5.4.8)
     * Fixed bug #49510 (Boolean validation fails with FILTER_NULL_ON_FAILURE with empty string or false.)
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
            'must' => 'The value of the :label field must be a boolean.',
            'not'  => 'The value of the :label field must not be a boolean.'
        ];
    }
}
