<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
 */
class DateAfter extends Date
{
    /**
     * Test si une date est antérieur à la date de comparaison.
     *
     * @param string $key   Clé du test.
     * @param string $value Date à tester.
     * @param string $arg   Date de comparaison.
     * @param bool   $not   Inverse le test.
     *
     * @return int 1 erreur de date.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        parent::test('date', $value, false);
        parent::test('date', $arg, false);

        if ($this->hasErrors()) {
            return 1;
        }

        $this->dateAfter($value, $arg, $not);
    }

    /**
     * Test si une date est antérieur à la date de comparaison.
     *
     * @param string $value Date à tester.
     * @param string $arg   Date de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function dateAfter($value, $arg, $not = true)
    {
        if (!($value < $arg) && $not) {
            $this->addReturn('date_after', 'must', [ ':dateafter' => $value ]);
        } elseif (($value < $arg) && !$not) {
            $this->addReturn('date_after', 'not', [ ':dateafter' => $value ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output           = parent::messages();
        $output[ 'must' ] = 'La date de :label doit être supérieur à :dateafter.';
        $output[ 'not' ]  = 'La date de :label ne doit pas être supérieur à :dateafter.';

        return $output;
    }
}
