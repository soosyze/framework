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
class MaxNumeric extends Max
{
    /**
     * Test si une valeur est plus grande que la valeur de comparaison.
     *
     * @param string  $key   Clé du test.
     * @param numeric $value Valeur à tester.
     * @param mixed   $args  Valeur de comparraison.
     * @param bool    $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        $length = $this->getSizeNumeric($value);
        if ($this->hasErrors()) {
            return;
        }
        if (!is_numeric($args)) {
            throw new \TypeError('The comparison argument must be numeric.');
        }
        $this->sizeMax($key, $length, $args, $not);
    }
}
