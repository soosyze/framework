<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToIntTest extends Filter
{
    /**
     * @dataProvider providerToInt
     *
     * @param int|string $value
     */
    public function testToInt(string $key, $value): void
    {
        $this->object
            ->addInput($key, $value)
            ->addRule($key, 'to_int')
            ->isValid();

        $this->assertTrue(is_int($this->object->getInput($key)));
    }

    public function providerToInt(): \Generator
    {
        /* Standard */
        yield [ 'int', 1234 ];
        yield [ 'txt', '1234' ];
        yield [ 'octal', 0123 ];
        yield [ 'hexa', 0x1A ];
        yield [ 'binaire', 0b11111111 ];
        yield [ 'zero', 0 ];
        yield [ 'zero_txt', '0' ];
        /* Cast type */
        yield [ 'cast', (int) 1.1 ];
        yield [ 'cast_txt', (int) '1.1' ];
        /* Limit */
        yield [ 'min', PHP_INT_MIN ];
        yield [ 'max', PHP_INT_MAX ];
    }

    public function testToIntException(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('field', 'error')
            ->addRule('field', 'to_int')
            ->isValid();
    }
}
