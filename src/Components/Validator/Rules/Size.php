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
     * Retourne la taille de comparaison.
     * Si la valeur est numérique elle est retournée directement.
     * Si la valeur est une taille exprimée en octet, elle et convertie en numérique puis retournée.
     *
     * @param int|string $size
     *
     * @throws \InvalidArgumentException The value must be numeric or in file size format.
     * @return int                       La Taille de comparaison.
     */
    protected function getComparator($size)
    {
        if (is_numeric($size)) {
            return $size;
        }
        $str = strtolower($size);
        if (preg_match('/^(\d+)(b|kb|mb|gb|tb|pb|eb|zb|yb)$/', $str, $matches)) {
            $units = [
                'b'  => 0, 'kb' => 1, 'mb' => 2,
                'gb' => 3, 'tb' => 4, 'pb' => 5,
                'eb' => 6, 'zb' => 7, 'yb' => 8
            ];
            $power = $units[ $matches[ 2 ] ];

            return pow(1024, $power) * $matches[ 1 ];
        }

        throw new \InvalidArgumentException('The value must be numeric or in file size format.');
    }

    /**
     * Retourne la longueur de valeur en fonction de son type.
     *
     * @param array|float|int|object|numeric|ressource|string|UploadedFileInterface $value Valeur à tester.
     *
     * @throws \InvalidArgumentException La fonction max ne peut pas tester pas ce type de valeur.
     * @return int|float                 Longueur.
     */
    protected function getSize($value)
    {
        if (is_numeric($value)) {
            /* numeric+0 = int|float */
            return $value + 0;
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
        }

        throw new \InvalidArgumentException('The between function can not test this type of value.');
    }
}
