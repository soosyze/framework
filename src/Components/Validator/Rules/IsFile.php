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
class IsFile extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est un fichier existant sur le serveur.
     *
     * @param string     $key   Clé du test.
     * @param string     $value Valeur à tester.
     * @param mixed|null $args  Argument de test.
     * @param bool       $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        if (!is_file($value) && $not) {
            $this->addReturn($key, 'must');
        } elseif (is_file($value) && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must be a valid path file.',
            'not'  => 'The :label field must not be a valid path file.'
        ];
    }
}
