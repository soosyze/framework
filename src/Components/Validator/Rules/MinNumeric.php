<?php

declare(strict_types=1);

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
class MinNumeric extends Min
{
    /**
     * Test si une valeur est plus petite que la valeur de comparaison.
     *
     * @param string  $key   Clé du test.
     * @param numeric $value Valeur à tester.
     * @param string  $args  Valeur de comparraison.
     * @param bool    $not   Inverse le test.
     *
     * @throws \InvalidArgumentException La valeur min n'est pas numérique.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        $length = $this->getSizeNumeric($value);
        if ($this->hasErrors()) {
            return;
        }
        $this->sizeMin($key, $length, $args, $not);
    }
}
