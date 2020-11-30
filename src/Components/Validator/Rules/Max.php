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
class Max extends Size
{
    /**
     * Test si une valeur est plus grande que la valeur de comparaison.
     *
     * @param string                                       $key   Clé du test.
     * @param int|float|string|array|UploadedFileInterface $value Valeur à tester.
     * @param string                                       $arg   Valeur de comparraison.
     * @param bool                                         $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        $length = $this->getSize($value);
        if ($this->hasErrors()) {
            return;
        }
        $this->sizeMax($key, $length, $arg, $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output           = parent::messages();
        $output[ 'must' ] = 'The :label field must not be greater than :max.';
        $output[ 'not' ]  = 'The :label field must be greater than :max.';

        return $output;
    }

    /**
     * Test si une valeur est plus grande que la valeur de comparaison.
     *
     * @param string    $key    Clé du test.
     * @param float|int $length Taille de la valeur.
     * @param string    $max    Valeur de comparraison.
     * @param bool      $not    Inverse le test.
     *
     * @return void
     */
    protected function sizeMax($key, $length, $max, $not)
    {
        $sizeMax = $this->getComparator($max);

        if (($length > $sizeMax) && $not) {
            $this->addReturn($key, 'must', [ ':max' => $max ]);
        } elseif (!($length > $sizeMax) && !$not) {
            $this->addReturn($key, 'not', [ ':max' => $max ]);
        }
    }
}
