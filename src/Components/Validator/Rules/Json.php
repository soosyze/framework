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
class Json extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si la valeur et de type JSON.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        json_decode($value);
        if (json_last_error() !== JSON_ERROR_NONE && $not) {
            $this->addReturn($key, 'must');
        } elseif (json_last_error() === JSON_ERROR_NONE && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label n\'est pas au format JSON.',
            'not'  => 'La valeur de :label ne doit pas être au format JSON.'
        ];
    }
}
