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
class Between extends Size
{
    /**
     * Test si une valeur est entre 2 valeurs de comparaison.
     *
     * @param string                                                        $key   Clé du test.
     * @param array|float|int|object|ressource|string|UploadedFileInterface $value Valeur à tester.
     * @param string                                                        $arg   Liste de 2 valeurs de comparaison séparées par une virgule.
     * @param bool                                                          $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        $between = $this->getParamMinMax($arg);

        $length = $this->getSize($value);
        $this->sizeBetween($key, $length, $between[ 'min' ], $between[ 'max' ], $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label doit être comprise entre les valeurs :min et :max.',
            'not'  => 'La valeur de :label ne doit pas être comprise entre les valeurs :min et :max.'
        ];
    }

    /**
     * Teste si une valeur est comprise entre 2 valeurs numériques.
     *
     * @param string  $key         Clé du test.
     * @param numeric $lengthValue Valeur de la taille.
     * @param numeric $min         Valeur minimum.
     * @param numeric $max         Valeur maximum.
     * @param bool    $not         Inverse le test.
     */
    protected function sizeBetween($key, $lengthValue, $min, $max, $not = true)
    {
        if (!($lengthValue <= $max && $lengthValue >= $min) && $not) {
            $this->addReturn($key, 'must', [ ':min' => $min, ':max' => $max ]);
        } elseif ($lengthValue <= $max && $lengthValue >= $min && !$not) {
            $this->addReturn($key, 'not', [ ':min' => $min, ':max' => $max ]);
        }
    }
}
