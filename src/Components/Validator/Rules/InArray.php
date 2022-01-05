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
class InArray extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est contenu dans un tableau.
     *
     * @param string $key   Clé du test.
     * @param scalar $value Valeur à tester.
     * @param mixed  $args  Tableau de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        if (is_string($args)) {
            $array = explode(',', $args);
        } elseif (is_array($args)) {
            $array = $args;
        } else {
            throw new \TypeError('The arguments must be a string or array.');
        }

        if (!in_array($value, $array) && $not) {
            $this->addReturn($key, 'must', [ ':list' => implode(', ', $array) ]);
        } elseif (in_array($value, $array) && !$not) {
            $this->addReturn($key, 'not', [ ':list' => implode(', ', $array) ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must be in the following list :list.',
            'not'  => 'The :label field must not be in the following list :list.'
        ];
    }
}
