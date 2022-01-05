<?php

declare(strict_types=1);

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
class ColorHex extends Regex
{
    /**
     * Test si la valeur correspond à une couleur au format hexadécimale à 3 ou 6 caractères.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param mixed  $args  Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        if (empty($args)) {
            $patern = '[\da-f]{6}|[\da-f]{3}';
        } elseif ($args === '3') {
            $patern = '[\da-f]{3}';
        } elseif ($args === '6') {
            $patern = '[\da-f]{6}';
        } else {
            throw new \InvalidArgumentException('A color in hexadecimal format must be contained in 3 or 6 characters.');
        }
        parent::test($key, $value, '/^#(' . $patern . ')$/i', $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must be a color in hexadecimal format.',
            'not'  => 'The :label field must not be a color in hexadecimal format.'
        ];
    }
}
