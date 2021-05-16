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
class Instance extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si la valeur est de type array.
     *
     * @param string $key    Clé du test.
     * @param string $values Valeur à tester.
     * @param string $args   Argument de test.
     * @param bool   $not    Inverse le test.
     */
    protected function test(string $key, $values, $args, bool $not): void
    {
        if (!($values instanceof $args) && $not) {
            $this->addReturn($key, 'must');
        } elseif ($values instanceof $args && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must be an instance of :class.',
            'not'  => 'The :label field must not be an instance of :class.'
        ];
    }
}
