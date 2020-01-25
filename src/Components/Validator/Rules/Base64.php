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
class Base64 extends \Soosyze\Components\Validator\Rule
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
        if (!preg_match('/^[a-zA-Z\d\/\r\n+]*={0,2}$/', $value) && $not) {
            $this->addReturn($key, 'must');
        } elseif (preg_match('/^[a-zA-Z\d\/\r\n+]*={0,2}$/', $value) && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'Le champ :label doit être encodé en base64.',
            'not'  => 'Le champ :label ne doit pas être encodé en base64.'
        ];
    }
}
