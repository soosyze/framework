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
class Dir extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est un répértoire existant sur le serveur.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        if (!is_dir($value) && $not) {
            $this->addReturn($key, 'must');
        } elseif (is_dir($value) && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'Le chemin de :label n\'est pas valide.',
            'not'  => 'Le chemin de :label n\'est pas valide.'
        ];
    }
}
