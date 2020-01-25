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
 * Pattern Semantic Versioning 2.0.0
 * The Semantic Versioning specification is authored by Tom Preston-Werner, inventor of Gravatar and cofounder of GitHub.
 *
 * @see https://github.com/semver/semver/blob/master/semver.md
 * @see https://semver.org/lang/fr/
 * @license Creative Commons - CC BY 3.0
 *
 * @author Mathieu NOËL
 */
class Version extends \Soosyze\Components\Validator\Rule
{
    const MAJOR = '(0|[1-9]\d*)\.';

    const MINOR = '(0|[1-9]\d*)\.';

    const PATH = '(0|[1-9]\d*)';

    const PRE_DELIVERY = '(?:-((?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?';

    const BUILD = '(?:\+([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?';

    /**
     * Test si une valeur est une URL.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        $pattern = self::MAJOR . self::MINOR . self::PATH . self::PRE_DELIVERY . self::BUILD;
        if (!preg_match("/^$pattern$/", $value) && $not) {
            $this->addReturn($key, 'must', [ ':regex' => $arg ]);
        } elseif (preg_match("/^$pattern$/", $value) && !$not) {
            $this->addReturn($key, 'not', [ ':regex' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label ne correspond pas à une version valide.',
            'not'  => 'La valeur de :label ne doit pas correspondre à une version valide.'
        ];
    }
}
