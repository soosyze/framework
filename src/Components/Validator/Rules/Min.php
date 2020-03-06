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
class Min extends Size
{
    /**
     * Test si une valeur est plus petite que la valeur de comparaison.
     *
     * @param string                                       $key   Clé du test.
     * @param int|float|string|array|UploadedFileInterface $value Valeur à tester.
     * @param int|float                                    $arg   Valeur de comparraison.
     * @param bool                                         $not   Inverse le test.
     *
     * @throws \InvalidArgumentException La valeur min n'est pas numérique.
     */
    protected function test($key, $value, $arg, $not)
    {
        $length = $this->getSize($value);
        if ($this->hasErrors()) {
            return 1;
        }
        $this->sizeMin($key, $length, $arg, $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output           = parent::messages();
        $output[ 'must' ] = 'The :label field must not be less than :min.';
        $output[ 'not' ]  = 'The :label field must be less than :min.';

        return $output;
    }

    /**
     * Test si une valeur est plus petite que la valeur de comparaison.
     *
     * @param string $key         Clé du test.
     * @param string $lengthValue Taille de la valeur.
     * @param string $min         Valeur de comparraison.
     * @param bool   $not         Inverse le test.
     */
    protected function sizeMin($key, $lengthValue, $min, $not)
    {
        $sizeMin = $this->getComparator($min);

        if ($lengthValue < $sizeMin && $not) {
            $this->addReturn($key, 'must', [ ':min' => $min ]);
        } elseif (!($lengthValue < $sizeMin) && !$not) {
            $this->addReturn($key, 'not', [ ':min' => $min ]);
        }
    }
}
