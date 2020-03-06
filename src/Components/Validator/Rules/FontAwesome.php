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
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class FontAwesome extends \Soosyze\Components\Validator\Rule
{
    /**
     * Liste des polices de caractères de FontAwesome.
     *
     * @var type
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
     * @param scalar $value Valeur à tester.
     * @param string $arg   Styles de fontawesome acceptés.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        $argStyle      = explode(',', $arg);
        $stylesPattern = [];
        foreach ($this->fonts as $char => $font) {
            if (in_array($font, $argStyle)) {
                $stylesPattern[ $char ] = $font;
            }
        }
        $str     = implode('|', array_keys($stylesPattern));
        $pattern = "/fa($str)? fa-[a-z]+/";
        if (!preg_match($pattern, $value) && $not) {
            $this->addReturn($key, 'must', [ ':list' => implode(',', $stylesPattern) ]);
        } elseif (preg_match($pattern, $value) && !$not) {
            $this->addReturn($key, 'not', [ ':list' => implode(',', $stylesPattern) ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'The :label field must correspond to one of the following FontAwesome styles :list.',
            'not'  => 'The :label field must not correspond to one of the following FontAwesome styles :list.'
        ];
    }
}
