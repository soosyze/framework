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
class ColorHex extends Regex
{
    /**
     * Test si la valeur correspond à une couleur au format hexadécimale à 3 ou 6 caractères.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        if ($arg === false) {
            $patern = '[A-Fa-f0-9]{6}|[A-Fa-f0-9]{3}';
        } elseif ($arg === '3') {
            $patern = '[A-Fa-f0-9]{3}';
        } elseif ($arg === '6') {
            $patern = '[A-Fa-f0-9]{6}';
        } else {
            throw new \InvalidArgumentException('A color in hexadecimal format must be contained in 3 or 6 characters.');
        }
        parent::test('colorhex', $value, '/^#(' . $patern . ')$/', $not);
    }
}
