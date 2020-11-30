<?php

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
class EqualStrict extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si 2 valeurs sont identiques.
     *
     * @param string $key   Clé du test.
     * @param scalar $value Valeur à tester.
     * @param string $arg   Valeur de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        if ($value !== $arg && $not) {
            $this->addReturn($key, 'must', [ ':value' => $arg ]);
        } elseif ($value === $arg && !$not) {
            $this->addReturn($key, 'not', [ ':value' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'The :label field must be strictly equal to :value.',
            'not'  => 'The :label field must not be strictly equal to :value.'
        ];
    }
}
