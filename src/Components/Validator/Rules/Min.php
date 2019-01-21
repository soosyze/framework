<?php

/**
 * Soosyze Framework http://soosyze.com
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
class Min extends Size
{

    /**
     * Test si une valeur est plus petite que la valeur de comparaison.
     *
     * @param string $key Clé du test.
     * @param int|float|string|array|UploadedFileInterface $value Valeur à tester.
     * @param int|float $arg Valeur de comparraison.
     * @param bool $not Inverse le test.
     *
     * @throws \InvalidArgumentException La valeur min n'est pas numérique.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        if (!is_numeric($arg)) {
            throw new \InvalidArgumentException('The min value must be numeric.');
        }

        $length = $this->getSize($value);
        $this->sizeMin($key, $length, $arg, $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label doit être au minimum :min.',
            'not'  => 'La valeur de :label ne doit pas dépasser :min.'
        ];
    }

    /**
     * Test si une valeur est plus petite que la valeur de comparaison.
     *
     * @param string $key Clé du test.
     * @param string $lengthValue Taille de la valeur.
     * @param string $min Valeur de comparraison.
     * @param bool $not Inverse le test.
     */
    protected function sizeMin($key, $lengthValue, $min, $not = true)
    {
        if ($lengthValue < $min && $not) {
            $this->addReturn($key, 'must', [ ':min' => $min ]);
        } elseif (!($lengthValue < $min) && !$not) {
            $this->addReturn($key, 'not', [ ':min' => $min ]);
        }
    }
}
