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
class FontAwesome extends \Soosyze\Components\Validator\Rule
{
    /**
     * Liste des polices de caractères de FontAwesome.
     *
     * @var array
     */
    protected $fonts = [
        /* FREE */
        'b' => 'brands',
        's' => 'solid',
        /* PRO ONLY */
        'r' => 'regular',
        'l' => 'light',
        'd' => 'duotone'
    ];

    /**
     * Test si une valeur est égale à une expression régulière.
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param string $args  Styles de fontawesome acceptés.
     * @param bool   $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        [ $pattern, $stylesPattern ] = $this->getPattern($args);

        if (!preg_match($pattern, $value) && $not) {
            $this->addReturn($key, 'must', [ ':list' => $stylesPattern ]);
        } elseif (preg_match($pattern, $value) && !$not) {
            $this->addReturn($key, 'not', [ ':list' => $stylesPattern ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field must correspond to one of the following FontAwesome styles :list.',
            'not'  => 'The :label field must not correspond to one of the following FontAwesome styles :list.'
        ];
    }

    private function getPattern(?string $args): array
    {
        if ($args === null) {
            return [ '/fa(b|s)? fa-[a-z]+/', 'brands,solid' ];
        }

        $argStyle = explode(',', $args);

        $stylesPattern = [];

        foreach ($this->fonts as $char => $font) {
            if (in_array($font, $argStyle)) {
                $stylesPattern[ $char ] = $font;
            }
        }
        $str = implode('|', array_keys($stylesPattern));

        return [ "/fa($str)? fa-[a-z]+/", implode(',', $stylesPattern) ];
    }
}
