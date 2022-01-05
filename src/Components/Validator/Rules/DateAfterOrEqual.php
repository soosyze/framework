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
class DateAfterOrEqual extends DateAfter
{
    /**
     * Test si une date est antérieur ou égale à la date de comparaison.
     *
     * @param string $key   Clé du test.
     * @param string $value Date à tester.
     * @param mixed  $args  Date de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        parent::test('date_after', $value, $args, $not);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key   Clé du test.
     * @param string $value Date à tester.
     * @param string $arg   Date de comparaison.
     * @param bool   $not   Inverse le test.
     */
    protected function testDateAfter(string $key, $value, $arg, bool $not): void
    {
        if ($value < $arg && $not) {
            $this->addReturn('date_after_or_equal', 'after', [ ':dateafter' => $arg ]);
        } elseif (($value >= $arg) && !$not) {
            $this->addReturn('date_after_or_equal', 'not_after', [ ':dateafter' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        $output                = parent::messages();
        $output[ 'after' ]     = 'The :label field must be greater than or equal to :dateafter.';
        $output[ 'not_after' ] = 'The :label field must not be a date greater than or equal to :dateafter.';

        return $output;
    }
}
