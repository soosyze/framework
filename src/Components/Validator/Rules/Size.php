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
abstract class Size extends \Soosyze\Components\Validator\Rule
{
    /**
     * Retourne la longueur de valeur en fonction de son type.
     *
     * @param array|float|int|object|ressource|string|UploadedFileInterface $value Valeur à tester.
     *
     * @throws \InvalidArgumentException La fonction max ne peut pas tester pas ce type de valeur.
     * @return int|float                 Longueur.
     */
    protected function getSize($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        if (is_string($value)) {
            return strlen($value);
        }
        if (is_array($value)) {
            return count($value);
        }
        if ($value instanceof UploadedFileInterface) {
            if ($value->getError() !== UPLOAD_ERR_OK) {
                return 0;
            }

            return $value->getStream()->getSize();
        }
        if (is_resource($value)) {
            $stats = fstat($value);

            return isset($stats[ 'size' ])
                ? $stats[ 'size' ]
                : 0;
        }
        if (is_object($value) && method_exists($value, '__toString')) {
            return strlen((string) $value);
        } else {
            throw new \InvalidArgumentException('The between function can not test this type of value.');
        }
    }
}
