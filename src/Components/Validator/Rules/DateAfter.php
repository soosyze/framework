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
        parent::test('date', $arg, false);
        if ($this->hasErrors()) {
            throw new \InvalidArgumentException('The comparison date is not correct.');
        }
        parent::test('date', $value, false);
        if ($this->hasErrors()) {
            return 1;
        }
        $this->testDateAfter($value, $arg, $not);
    }

    /**
     * Test si une date est antérieur à la date de comparaison.
     *
     * @param string $value Date à tester.
     * @param string $arg   Date de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function testDateAfter($value, $arg, $not = true)
    {
        if (!($value > $arg) && $not) {
            $this->addReturn('date_after', 'after', [ ':dateafter' => $arg ]);
        } elseif (($value > $arg) && !$not) {
            $this->addReturn('date_after', 'not_after', [ ':dateafter' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output                = parent::messages();
        $output[ 'after' ]     = 'La date de :label doit être supérieur à :dateafter.';
        $output[ 'not_after' ] = 'La date de :label ne doit pas être supérieur à :dateafter.';

        return $output;
    }
}
