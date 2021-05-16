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
class AlphaNumText extends Regex
{
    /**
     * Test si la valeur est alpha numérique et possède des caractères textuelles [a-zA-Z0-9 .!?,;:_-].
     *
     * @param string     $key   Clé du test.
     * @param string     $value Valeur à tester.
     * @param mixed|null $args  Argument de test.
     * @param bool       $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        parent::test($key, $value, '/^[\w\s.!?,;:_-…]*$/i', $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must contain only letters, numbers and punctuation characters.',
            'not'  => 'The :label field must not contain letters, numbers, and punctuation characters.'
        ];
    }
}
