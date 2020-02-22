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
class FloatType extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une variable est de type ou de valeur numérique flottant.
     *
     * @param string $key   Clé du test.
     * @param float  $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        $filter = filter_var($value, FILTER_VALIDATE_FLOAT);
        if (!is_float($filter) && $not) {
            $this->addReturn($key, 'must');
        } elseif (is_float($filter) && !$not) {
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
            'must' => 'La valeur de :label doit être nombre flottant.',
            'not'  => 'La valeur de :label ne doit pas être un nombre flottant.'
        ];
    }
}
