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
class DateBefore extends Date
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
        parent::test('date', $arg, false, true);
        if ($this->hasErrors()) {
            throw new \InvalidArgumentException('The comparison date is not correct.');
        }
        parent::test('date', $value, false, true);
        if ($this->hasErrors()) {
            return 1;
        }
        $this->testDateBefore($key, $value, $arg, $not);
    }

    /**
     * Test si une date est antérieur à la date de comparaison.
     *
     * @param string $value Date à tester.
     * @param string $arg   Date de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function testDateBefore($key, $value, $arg, $not)
    {
        if (!($value < $arg) && $not) {
            $this->addReturn($key, 'before', [ ':datebefore' => $arg ]);
        } elseif (($value < $arg) && !$not) {
            $this->addReturn($key, 'not_before', [ ':datebefore' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output                 = parent::messages();
        $output[ 'before' ]     = 'La date de :label doit être inférieur à :datebefore.';
        $output[ 'not_before' ] = 'La date de :label ne doit pas être inferieur à :datebefore.';

        return $output;
    }
}
