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
class DateFormat extends Date
{
    /**
     * Test si une date correspond au format.
     *
     * @see http://php.net/manual/fr/datetime.createfromformat.php
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg   Format de la date (ex: Y-m-d).
     * @param bool   $not   Inverse le test.
     *
     * @return int 1 erreur de date.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        parent::test('date', $value, $arg);

        if ($this->hasErrors()) {
            return 1;
        }

        $dateFormat  = date_parse_from_format($arg, $value);
        $errorFormat = $dateFormat[ 'error_count' ] === 0 && $dateFormat[ 'warning_count' ] === 0;

        if (!$errorFormat && $not) {
            $this->addReturn('date_format', 'must', [ $arg ]);
        } elseif ($errorFormat && !$not) {
            $this->addReturn('date_format', 'not', [ $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output           = parent::messages();
        $output[ 'must' ] = 'La valeur de :label n\'est pas au format :label.';
        $output[ 'not' ]  = 'La valeur de :label ne doit pas être du format :label.';

        return $output;
    }
}
