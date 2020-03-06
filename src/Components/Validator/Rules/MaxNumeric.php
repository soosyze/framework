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
     * @param string    $key   Clé du test.
     * @param numeric   $value Valeur à tester.
     * @param int|float $arg   Valeur de comparraison.
     * @param bool      $not   Inverse le test.
     *
     * @throws \InvalidArgumentException La valeur max n'est pas numérique.
     */
    protected function test($key, $value, $arg, $not)
    {
        $length = $this->getSizeNumeric($value);
        if ($this->hasErrors()) {
            return 1;
        }
        $this->sizeMax($key, $length, $arg, $not);
    }
}
