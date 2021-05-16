<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToFloatTest extends Filter
{
    /**
     * @dataProvider providerToFloat
     *
     * @param float|int|string $value
     */
    public function testToFloat(string $key, $value): void
    {
        $this->object
            ->addInput($key, $value)
            ->addRule($key, 'to_float')
            ->isValid();

        $this->assertTrue(is_float($this->object->getInput($key)));
    }

    public function providerToFloat(): \Generator
    {
        /* Standard */
        yield [ 'float', 1.0 ];
        yield [ 'txt', '1.0' ];
        yield [ 'decimal', 1 ];
        yield [ 'decimal_txt', '1' ];
        yield [ 'zero', 0 ];
        yield [ 'zero_txt', '0' ];
        /* Exponent */
        yield [ 'exp', 1.0e1 ];
        yield [ 'exp_2', 1E1 ];
        yield [ 'exp_txt', '1.0e1' ];
        yield [ 'exp_txt_2', '1E1' ];
        /* Limit */
        yield [ 'min', PHP_INT_MIN - 1 ];
        yield [ 'max', PHP_INT_MAX + 1 ];
        /* Cast type */
        yield [ 'cast', (float) 1 ];
        yield [ 'cast_txt', (float) '1' ];
    }

    public function testToFloatException(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('field', 'error')
            ->addRule('field', 'to_float')
            ->isValid();
    }
}
