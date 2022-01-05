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
class Ip extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est une adresse IP.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param mixed  $args  Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        $version = 'IP';
        if (empty($args)) {
            $options = null;
        } elseif ($args === '4') {
            $version = 'IPv4';
            $options = FILTER_FLAG_IPV4;
        } elseif ($args === '6') {
            $version = 'IPv6';
            $options = FILTER_FLAG_IPV6;
        } else {
            throw new \InvalidArgumentException('An IP address must be in IPv4 or IPv6 format.');
        }

        if (!filter_var($value, FILTER_VALIDATE_IP, $options) && $not) {
            $this->addReturn($key, 'must', [ ':version' => $version ]);
        } elseif (filter_var($value, FILTER_VALIDATE_IP, $options) && !$not) {
            $this->addReturn($key, 'not', [ ':version' => $version ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must be a valid :version address.',
            'not'  => 'The :label field must not be a valid :version address.'
        ];
    }
}
