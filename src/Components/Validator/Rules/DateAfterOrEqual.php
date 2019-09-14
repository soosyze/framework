<?php

/**
 * Soosyze Framework https://soosyze.com
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
class DateAfterOrEqual extends DateAfter
{
    /**
     * Test si une date est antérieur ou égale à la date de comparaison.
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
        parent::test('date_after', $value, $arg, $not);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $value Date à tester.
     * @param string $arg   Date de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function testDateAfter($value, $arg, $not = true)
    {
        if (!($value >= $arg) && $not) {
            $this->addReturn('date_after_or_equal', 'after', [ ':dateafter' => $arg ]);
        } elseif (($value >= $arg) && !$not) {
            $this->addReturn('date_after_or_equal', 'not_after', [ ':dateafter' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output                = parent::messages();
        $output[ 'after' ]     = 'La date de :label doit être supérieur ou égale à :dateafter.';
        $output[ 'not_after' ] = 'La date de :label ne doit pas être supérieur ou égale à :dateafter.';

        return $output;
    }
}
