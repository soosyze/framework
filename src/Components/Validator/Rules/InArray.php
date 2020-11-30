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
class InArray extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est contenu dans un tableau.
     *
     * @param string $key   Clé du test.
     * @param scalar $value Valeur à tester.
     * @param string $arg   Tableau de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        $array = explode(',', $arg);

        if (!in_array($value, $array) && $not) {
            $this->addReturn($key, 'must', [ ':list' => $arg ]);
        } elseif (in_array($value, $array) && !$not) {
            $this->addReturn($key, 'not', [ ':list' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'The :label field must be in the following list :list.',
            'not'  => 'The :label field must not be in the following list :list.'
        ];
    }
}
