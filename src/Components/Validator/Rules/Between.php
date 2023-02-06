<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Soosyze\Components\Validator\Comparators\MinMax;

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
     * @param string                                                       $key   Clé du test.
     * @param array|float|int|object|resource|string|UploadedFileInterface $value Valeur à tester.
     * @param mixed                                                        $args  Liste de 2 valeurs de comparaison séparées par une virgule.
     * @param bool                                                         $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        $length = $this->getSize($value);

        if ($this->hasErrors()) {
            return;
        }
        if (!is_string($args)) {
            throw new \TypeError('The comparisons arguments must be a string.');
        }

        $this->sizeBetween($key, $length, $this->getParamMinMax($args), $not);
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        $output           = parent::messages();
        $output['must'] = 'The :label field must be between :min and :max.';
        $output['not']  = 'The :label field must not be between :min and :max.';

        return $output;
    }

    /**
     * Teste si une valeur est comprise entre 2 valeurs numériques.
     *
     * @param string  $key    Clé du test.
     * @param numeric $length Valeur de la taille.
     * @param bool    $not    Inverse le test.
     */
    protected function sizeBetween(
        string $key,
        $length,
        MinMax $comparator,
        bool $not
    ): void {
        if (!($length <= $comparator->getComparatorMax() && $length >= $comparator->getComparatorMin()) && $not) {
            $this->addReturn($key, 'must', [
                ':min' => $comparator->getValueMin(),
                ':max' => $comparator->getValueMax()
            ]);
        } elseif ($length <= $comparator->getComparatorMax() && $length >= $comparator->getComparatorMin() && !$not) {
            $this->addReturn($key, 'not', [
                ':min' => $comparator->getValueMin(),
                ':max' => $comparator->getValueMax()
            ]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException Between values are invalid.
     * @throws \InvalidArgumentException The minimum value must not be greater than the maximum value.
     */
    protected function getParamMinMax(string $args): MinMax
    {
        $explode = explode(',', $args);
        if (!isset($explode[0], $explode[1])) {
            throw new \InvalidArgumentException('Between values are invalid.');
        }

        $comparatorMin = $this->getComparator($explode[0]);
        $comparatorMax = $this->getComparator($explode[1]);

        if ($comparatorMin > $comparatorMax) {
            throw new \InvalidArgumentException('The minimum value must not be greater than the maximum value.');
        }

        return MinMax::create(
            $explode[ 0 ],
            $explode[ 1 ],
            $comparatorMin,
            $comparatorMax
        );
    }
}
