<?php

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
        if (preg_match('/^(\d+)(b|kb|mb|gb|tb|pb|eb|zb|yb)$/i', $str, $matches)) {
            $units = [
                'b'  => 0, 'kb' => 1, 'mb' => 2,
                'gb' => 3, 'tb' => 4, 'pb' => 5,
                'eb' => 6, 'zb' => 7, 'yb' => 8
            ];
            $power = $units[ strtolower($matches[ 2 ]) ];

            return pow(1024, $power) * $matches[ 1 ];
        }

        throw new \InvalidArgumentException('The value must be numeric or in file size format.');
    }

    /**
     * Retourne la longueur de valeur en fonction de son type.
     *
     * @param array|float|int|object|ressource|string|UploadedFileInterface $value Valeur à tester.
     *
     * @return int|float Longueur.
     */
    protected function getSize($value)
    {
        $size = 0;
        if (is_int($value) || is_float($value)) {
            $size = $value;
        } elseif (is_string($value) || method_exists($value, '__toString')) {
            $size = strlen((string) $value);
        } elseif (is_array($value)) {
            $size = count($value);
        } elseif ($value instanceof UploadedFileInterface) {
            $size = $value->getError() === UPLOAD_ERR_OK
                ? $value->getStream()->getSize()
                : 0;
        } elseif (is_resource($value)) {
            $stats = fstat($value);

            $size = isset($stats[ 'size' ])
                ? $stats[ 'size' ]
                : 0;
        } else {
            $this->addReturn('size', 'size');
        }

        return $size;
    }

    /**
     * Retourne la taille de la valeur.
     *
     * @param numeric $value Valeur à tester.
     *
     * @return int|float
     */
    protected function getSizeNumeric($value)
    {
        $size = 0;
        if (is_numeric($value)) {
            /* numeric+0 = int|float */
            $size = $value + 0;
        } else {
            $this->addReturn('size', 'size_numeric');
        }

        return $size;
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'size'         => 'The value of the :label field must be of integer, floating point, character string, array, file or resource type.',
            'size_numeric' => 'The value of the :label field must be numeric.'
        ];
    }
}
