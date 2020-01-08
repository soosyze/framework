<?php

/**
 * Soosyze Framework https://soosyze.com
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
    protected function test($key, $value, $arg, $not)
    {
        $length = $this->getSize($value);
        if ($this->hasErrors()) {
            return 1;
        }
        $between = $this->getParamMinMax($arg);
        $this->sizeBetween($key, $length, $between[ 'min' ], $between[ 'max' ], $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output           = parent::messages();
        $output[ 'must' ] = 'La valeur de :label doit être comprise entre les valeurs :min et :max.';
        $output[ 'not' ]  = 'La valeur de :label ne doit pas être comprise entre les valeurs :min et :max.';

        return $output;
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
    protected function sizeBetween($key, $lengthValue, $min, $max, $not)
    {
        if (!($lengthValue <= $max[ 'size' ] && $lengthValue >= $min[ 'size' ]) && $not) {
            $this->addReturn($key, 'must', [ ':min' => $min[ 'value' ], ':max' => $max[ 'value' ] ]);
        } elseif ($lengthValue <= $max[ 'size' ] && $lengthValue >= $min[ 'size' ] && !$not) {
            $this->addReturn($key, 'not', [ ':min' => $min[ 'value' ], ':max' => $max[ 'value' ] ]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param type $arg
     *
     * @throws \InvalidArgumentException
     * @return type
     */
    protected function getParamMinMax($arg)
    {
        $explode = explode(',', $arg);
        if (!isset($explode[ 0 ], $explode[ 1 ])) {
            throw new \InvalidArgumentException('Between values are invalid.');
        }

        $min = $this->getComparator($explode[ 0 ]);
        $max = $this->getComparator($explode[ 1 ]);

        if ($min > $max) {
            throw new \InvalidArgumentException('The minimum value must not be greater than the maximum value.');
        }

        return [
            'min' => [
                'value' => $explode[ 0 ],
                'size'  => $min
            ],
            'max' => [
                'value' => $explode[ 1 ],
                'size'  => $max
            ]
        ];
    }
}
