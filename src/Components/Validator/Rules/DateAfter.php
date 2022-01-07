<?php

declare(strict_types=1);

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
class DateAfter extends Date
{
    /**
     * Test si une date est antérieur à la date de comparaison.
     *
     * @param string $key   Clé du test.
     * @param string $value Date à tester.
     * @param mixed  $args  Date de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        if (!is_string($args)) {
            throw new \TypeError('The comparison argument must be a string.');
        }
        parent::test('date', $args, '', true);

        if ($this->hasErrors()) {
            throw new \InvalidArgumentException('The comparison date is not correct.');
        }
        parent::test('date', $value, '', true);

        if ($this->hasErrors()) {
            return;
        }

        $this->testDateAfter($key, $value, $args, $not);
    }

    /**
     * Test si une date est antérieur à la date de comparaison.
     *
     * @param string $key   Clé du test.
     * @param string $value Date à tester.
     * @param string $arg   Date de comparaison.
     * @param bool   $not   Inverse le test.
     *
     * @return void
     */
    protected function testDateAfter(string $key, $value, $arg, bool $not): void
    {
        if ($value <= $arg && $not) {
            $this->addReturn($key, 'after', [ ':dateafter' => $arg ]);
        } elseif (($value > $arg) && !$not) {
            $this->addReturn($key, 'not_after', [ ':dateafter' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        $output                = parent::messages();
        $output[ 'after' ]     = 'The :label field must be a date greater than :dateafter.';
        $output[ 'not_after' ] = 'The :label field must not be a date greater than :dateafter.';

        return $output;
    }
}
