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
class Max extends Size
{
    /**
     * Test si une valeur est plus grande que la valeur de comparaison.
     *
     * @param string                                       $key   Clé du test.
     * @param int|float|string|array|UploadedFileInterface $value Valeur à tester.
     * @param int|float                                    $arg   Valeur de comparraison.
     * @param bool                                         $not   Inverse le test.
     *
     * @throws \InvalidArgumentException La valeur max n'est pas numérique.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        if (!is_numeric($arg)) {
            throw new \InvalidArgumentException('The max value must be numeric.');
        }

        $length = $this->getSize($value);
        $this->sizeMax($key, $length, $arg, $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label doit être au maximum :max.',
            'not'  => 'La valeur de :label doit dépasser :max.'
        ];
    }

    /**
     * Test si une valeur est plus grande que la valeur de comparaison.
     *
     * @param string $key         Clé du test.
     * @param string $lengthValue Taille de la valeur.
     * @param string $max         Valeur de comparraison.
     * @param bool   $not         Inverse le test.
     */
    protected function sizeMax($key, $lengthValue, $max, $not = true)
    {
        if (($lengthValue > $max) && $not) {
            $this->addReturn($key, 'must', [ ':max' => $max ]);
        } elseif (!($lengthValue > $max) && !$not) {
            $this->addReturn($key, 'not', [ ':max' => $max ]);
        }
    }
}
