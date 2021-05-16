<?php

namespace Soosyze\Tests\Resources\Validator;

use Soosyze\Components\Validator\Rule;

class Cube extends Rule
{
    protected function test(string $key, $value, $arg, bool $not = true): void
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException();
        }
        if ($value * $value != $arg && $not) {
            $this->addReturn($key, 'must');
        } elseif ($value * $value == $arg && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    protected function messages(): array
    {
        return [
            'must' => 'La valeur au cube de :label n\'est pas égale à 4.',
            'not'  => 'La valeur au cube de :label ne doit pas être égale à 4.'
        ];
    }
}
