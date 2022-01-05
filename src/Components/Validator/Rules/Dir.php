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
class Dir extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est un répértoire existant sur le serveur.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param mixed  $args  Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
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
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must be a valid path.',
            'not'  => 'The :label field must not be a valid path.'
        ];
    }
}
