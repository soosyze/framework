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
class BetweenNumeric extends Between
{
    /**
     * Test si une valeur est entre 2 valeurs de comparaison.
     *
     * @param string  $key   Clé du test.
     * @param numeric $value Valeur à tester.
     * @param string  $args  Liste de 2 valeurs de comparaison séparées par une virgule.
     * @param bool    $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        $length = $this->getSizeNumeric($value);

        if ($this->hasErrors()) {
            return;
        }

        [ $min, $max ] = $this->getParamMinMax($args);
        $this->sizeBetween($key, $length, $min, $max, $not);
    }
}
