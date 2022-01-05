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
class Slug extends Regex
{
    /**
     * Test si la valeur correspond à une chaine de caractères alpha numérique (underscore et tiret autorisé).
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param mixed  $args  Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        parent::test($key, $value, '/^[a-zA-Z0-9_-]*$/', $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must contain only letters, numbers, dashes and anderscore.',
            'not'  => 'The :label field must not contain letters, numbers, dashes and anderscore.'
        ];
    }
}
