<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
 */
class Ip extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est une adresse IP.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        $version = 'IP';
        if ($arg === false) {
            $options = null;
        } elseif ($arg === '4') {
            $version = 'IPv4';
            $options = FILTER_FLAG_IPV4;
        } elseif ($arg === '6') {
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
    protected function messages()
    {
        return [
            'must' => 'Le champ :label doit être une adresse :version valide.',
            'not'  => 'Le champ :label ne doit pas être une adresse :version valide.'
        ];
    }
}
