<?php

/**
 * Soosyze Framework http://soosyze.com
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
class AlphaNumText extends Regex
{

    /**
     * Test si la valeur est alpha numérique et possède des caractères textuelles [a-zA-Z0-9 .!?,;:_-].
     *
     * @param string $key Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg Argument de test.
     * @param bool $not Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        parent::test('alphanumtext', $value, '/^[a-zA-Z0-9 .!?,;:_-]*$/', $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label contient des caractères non alpha numérique et/ou non textuelle.',
            'not'  => 'La valeur de :label ne doit pas contenir des caractères non alpha numérique et/ou non textuelle.'
        ];
    }
}
