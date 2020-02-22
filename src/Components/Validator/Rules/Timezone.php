<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

/**
 * {@inheritdoc}
 *
 * @see https://github.com/semver/semver/blob/master/semver.md
 * @see https://semver.org/lang/fr/
 * @license Creative Commons - CC BY 3.0
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Timezone extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est une URL.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        if (!in_array($value, timezone_identifiers_list()) && $not) {
            $this->addReturn($key, 'must');
        } elseif (in_array($value, timezone_identifiers_list()) && !$not) {
            $this->addReturn($key, 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur du champ :label doit être un fuseau horaire valide.',
            'not'  => 'La valeur du champ :label ne doit pas être un fuseau horaire valide.'
        ];
    }
}
