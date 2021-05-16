<?php

namespace Soosyze\Tests\Components\Validator\Filters;

class ToBoolTest extends Filter
{
    /**
     * @dataProvider providerToBool
     *
     * @param bool|int|string $value
     */
    public function testToBool(string $key, $value): void
    {
        $this->object
            ->addInput($key, $value)
            ->addRule($key, 'to_bool')
            ->isValid();

        $this->assertTrue(is_bool($this->object->getInput($key)));
    }

    public function providerToBool(): \Generator
    {
        /* True */
        yield [ 'true', true ];
        yield [ 'true_txt', 'true' ];
        yield [ 'true_one', 1 ];
        yield [ 'true_one_txt', '1' ];
        yield [ 'true_on', 'on' ];
        yield [ 'true_yes', 'yes' ];
        /* False */
        yield [ 'false', false ];
        yield [ 'false_text', 'false' ];
        yield [ 'false_zero', 0 ];
        yield [ 'false_zero_txt', '0' ];
        yield [ 'false_off', 'off' ];
        yield [ 'false_no', 'no' ];
        yield [ 'false_void', '' ];
    }

    public function testToBoolException(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('field', 'error')
            ->addRule('field', 'to_bool')
            ->isValid();
    }
}
