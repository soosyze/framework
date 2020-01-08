<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Psr\Http\Message\UploadedFileInterface;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
 */
class MinNumeric extends Min
{
    /**
     * Test si une valeur est plus petite que la valeur de comparaison.
     *
     * @param string    $key   Clé du test.
     * @param numeric   $value Valeur à tester.
     * @param int|float $arg   Valeur de comparraison.
     * @param bool      $not   Inverse le test.
     *
     * @throws \InvalidArgumentException La valeur min n'est pas numérique.
     */
    protected function test($key, $value, $arg, $not)
    {
        $length = $this->getSizeNumeric($value);
        if ($this->hasErrors()) {
            return 1;
        }
        $this->sizeMin($key, $length, $arg, $not);
    }
}
