<?php

namespace Soosyze\Tests\Resources\Validator;

use Soosyze\Components\Validator\Rule;

class DoubleR extends Rule
{
    protected function test(string $key, $value, $arg, bool $not = true): void
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException();
        }
        if ($value * 2 != 16 && $not) {
            $this->addReturn($key, 'must');
        } elseif ($value * 2 == 16 && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    protected function messages(): array
    {
        return [
            'must' => 'Le double de la valeur de :label n\'est pas égale à 16.',
            'not'  => 'Le double de la valeur de :label ne doit pas être égale à 16.'
        ];
    }
}
