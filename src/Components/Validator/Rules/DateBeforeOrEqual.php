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
class DateBeforeOrEqual extends DateBefore
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
    protected function test($key, $value, $arg, $not)
    {
        parent::test('date_before', $value, $arg, $not);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $value Date à tester.
     * @param string $arg   Date de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function testDateBefore($value, $arg, $not)
    {
        if (!($value <= $arg) && $not) {
            $this->addReturn('date_before_or_equal', 'before', [ ':datebefore' => $arg ]);
        } elseif (($value <= $arg) && !$not) {
            $this->addReturn('date_before_or_equal', 'not_before', [ ':datebefore' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output                 = parent::messages();
        $output[ 'before' ]     = 'La date de :label doit être inférieur ou égale à :datebefore.';
        $output[ 'not_before' ] = 'La date de :label ne doit pas être inferieur ou égale à :datebefore.';

        return $output;
    }
}
