<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Psr\Http\Message\UploadedFileInterface;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Min extends Size
{
    /**
     * Test si une valeur est plus petite que la valeur de comparaison.
     *
     * @param string                                       $key   Clé du test.
     * @param int|float|string|array|UploadedFileInterface $value Valeur à tester.
     * @param mixed                                        $args  Valeur de comparraison.
     * @param bool                                         $not   Inverse le test.
     *
     * @throws \InvalidArgumentException La valeur min n'est pas numérique.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        $length = $this->getSize($value);
        if ($this->hasErrors()) {
            return;
        }
        if (!is_numeric($args) && !is_string($args)) {
            throw new \TypeError('The comparison argument must be a string or numeric.');
        }
        $this->sizeMin($key, $length, $args, $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        $output           = parent::messages();
        $output[ 'must' ] = 'The :label field must not be less than :min.';
        $output[ 'not' ]  = 'The :label field must be less than :min.';

        return $output;
    }

    /**
     * Test si une valeur est plus petite que la valeur de comparaison.
     *
     * @param string         $key    Clé du test.
     * @param float|int      $length Taille de la valeur.
     * @param numeric|string $min    Valeur de comparraison.
     * @param bool           $not    Inverse le test
     *
     * @return void
     */
    protected function sizeMin(string $key, $length, $min, bool $not): void
    {
        $sizeMin = $this->getComparator($min);

        if ($length < $sizeMin && $not) {
            $this->addReturn($key, 'must', [ ':min' => $min ]);
        } elseif ($length >= $sizeMin && !$not) {
            $this->addReturn($key, 'not', [ ':min' => $min ]);
        }
    }
}
