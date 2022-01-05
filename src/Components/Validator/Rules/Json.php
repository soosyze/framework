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
class Json extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si la valeur et de type JSON.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param mixed  $args  Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        json_decode($value);
        if (json_last_error() !== JSON_ERROR_NONE && $not) {
            $this->addReturn($key, 'must');
        } elseif (json_last_error() === JSON_ERROR_NONE && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must be in JSON format.',
            'not'  => 'The :label field must not be in JSON format.'
        ];
    }
}
