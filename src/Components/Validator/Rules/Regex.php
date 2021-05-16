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
class Regex extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est égale à une expression régulière.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $args  Expression régulière.
     * @param bool   $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        if (!preg_match($args, $value) && $not) {
            $this->addReturn($key, 'must', [ ':regex' => $args ]);
        } elseif (preg_match($args, $value) && !$not) {
            $this->addReturn($key, 'not', [ ':regex' => $args ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must correspond to the validation rule :regex',
            'not'  => 'The :label field must not correspond to the validation rule :regex'
        ];
    }
}
