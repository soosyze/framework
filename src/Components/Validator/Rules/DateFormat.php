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
class DateFormat extends \Soosyze\Components\Validator\Rule
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
    protected function test($key, $value, $arg, $not)
    {
        $dateFormat  = date_parse_from_format($arg, $value);
        $errorFormat = $dateFormat[ 'error_count' ] === 0 && $dateFormat[ 'warning_count' ] === 0;

        if (!$errorFormat && $not) {
            $this->addReturn($key, 'must', [ ':format' => $arg ]);
        } elseif ($errorFormat && !$not) {
            $this->addReturn($key, 'not', [ ':format' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label n\'est pas au format de date :format.',
            'not'  => 'La valeur de :label ne doit pas être du format de date :format.'
        ];
    }
}
