<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
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
            return;
        }

        list($min, $max) = $this->getParamMinMax($arg);
        $this->sizeBetween($key, $length, $min, $max, $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output           = parent::messages();
        $output[ 'must' ] = 'The :label field must be between :min and :max.';
        $output[ 'not' ]  = 'The :label field must not be between :min and :max.';

        return $output;
    }

    /**
     * Teste si une valeur est comprise entre 2 valeurs numériques.
     *
     * @param string  $key    Clé du test.
     * @param numeric $length Valeur de la taille.
     * @param array   $min    Valeur minimum.
     * @param array   $max    Valeur maximum.
     * @param bool    $not    Inverse le test.
     *
     * @return void
     */
    protected function sizeBetween($key, $length, array $min, array $max, $not)
    {
        if (!($length <= $max[ 'size' ] && $length >= $min[ 'size' ]) && $not) {
            $this->addReturn($key, 'must', [ ':min' => $min[ 'value' ], ':max' => $max[ 'value' ] ]);
        } elseif ($length <= $max[ 'size' ] && $length >= $min[ 'size' ] && !$not) {
            $this->addReturn($key, 'not', [ ':min' => $min[ 'value' ], ':max' => $max[ 'value' ] ]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param string $arg
     *
     * @throws \InvalidArgumentException Between values are invalid.
     * @throws \InvalidArgumentException The minimum value must not be greater than the maximum value.
     *
     * @return array
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
            [
                'value' => $explode[ 0 ],
                'size'  => $min
            ],
            [
                'value' => $explode[ 1 ],
                'size'  => $max
            ]
        ];
    }
}
