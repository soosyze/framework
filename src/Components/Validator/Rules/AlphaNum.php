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
class AlphaNum extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si la valeur est Alpha numérique [a-zA-Z0-9].
     *
     * @param string     $key   Clé du test.
     * @param string     $value Valeur à tester.
     * @param mixed|null $args  Argument de test.
     * @param bool       $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        if (!ctype_alnum($value) && $not) {
            $this->addReturn($key, 'must');
        } elseif (ctype_alnum($value) && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must contain only letters and numbers.',
            'not'  => 'The :label field must not contain letters and numbers.'
        ];
    }
}
