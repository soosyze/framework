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
class DateBeforeOrEqual extends DateBefore
{
    /**
     * Test si une date est antérieur à la date de comparaison.
     *
     * @param string $key   Clé du test.
     * @param string $value Date à tester.
     * @param string $arg   Date de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        parent::test('date_before', $value, $arg, $not);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key   Clé du test.
     * @param string $value Date à tester.
     * @param string $arg   Date de comparaison.
     * @param bool   $not   Inverse le test.
     *
     * @return void
     */
    protected function testDateBefore($key, $value, $arg, $not)
    {
        if (!($value <= $arg) && $not) {
            $this->addReturn($key, 'before', [ ':datebefore' => $arg ]);
        } elseif (($value <= $arg) && !$not) {
            $this->addReturn($key, 'not_before', [ ':datebefore' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output                 = parent::messages();
        $output[ 'before' ]     = 'The :label field must be less than or equal to :datebefore.';
        $output[ 'not_before' ] = 'The :label field must not be a date less than or equal to :datebefore.';

        return $output;
    }
}
