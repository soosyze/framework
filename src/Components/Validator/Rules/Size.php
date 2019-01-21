<?php

/**
 * Soosyze Framework http://soosyze.com
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
abstract class Size extends \Soosyze\Components\Validator\Rule
{

    /**
     * Retourne la longueur de valeur en fonction de son type.
     *
     * @param array|float|int|object|ressource|string $value Valeur à tester.
     *
     * @return int|float Longueur.
     *
     * @throws \InvalidArgumentException La fonction max ne peut pas tester pas ce type de valeur.
     */
    protected function getSize($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        } elseif (is_string($value)) {
            return strlen($value);
        } elseif (is_array($value)) {
            return count($value);
        } elseif (is_resource($value)) {
            $stats = fstat($value);

            return isset($stats[ 'size' ])
                ? $stats[ 'size' ]
                : 0;
        } elseif (is_object($value) && method_exists($value, '__toString')) {
            return strlen(( string ) $value);
        } else {
            throw new \InvalidArgumentException('The between function can not test this type of value.');
        }
    }
}