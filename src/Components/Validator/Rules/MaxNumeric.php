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
class MaxNumeric extends Max
{
    /**
     * Test si une valeur est plus grande que la valeur de comparaison.
     *
     * @param string  $key   Clé du test.
     * @param numeric $value Valeur à tester.
     * @param string  $arg   Valeur de comparraison.
     * @param bool    $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        $length = $this->getSizeNumeric($value);
        if ($this->hasErrors()) {
            return;
        }
        $this->sizeMax($key, $length, $arg, $not);
    }
}
