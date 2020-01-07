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
class BetweenNumeric extends Between
{
    /**
     * Test si une valeur est entre 2 valeurs de comparaison.
     *
     * @param string  $key   Clé du test.
     * @param numeric $value Valeur à tester.
     * @param string  $arg   Liste de 2 valeurs de comparaison séparées par une virgule.
     * @param bool    $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        $length = $this->getSizeNumeric($value);
        if ($this->hasErrors()) {
            return 1;
        }
        $between = $this->getParamMinMax($arg);
        $this->sizeBetween($key, $length, $between[ 'min' ], $between[ 'max' ], $not);
    }
}
