<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Form;

/**
 * Génèration des champs de formulaire.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
trait FormRenderTrait
{
    /**
     * Balises autorisées pour les groupes de formulaire.
     *
     * @var string[]
     */
    protected static $baliseGroup = [
        'div', 'span', 'fieldset'
    ];

    /**
     * Attributs Boolean HTML.
     *
     * @see https://www.w3.org/TR/html52/infrastructure.html#sec-boolean-attributes
     *
     * @var string[]
     */
    protected static $attributesUnique = [
        'autofocus',
        'checked',
        'disabled',
        'multiple',
        'readonly',
        'required'
    ];

    /**
     * Met en forme les attributs pour les balises inputs standards.
     *
     * @param array $attr Listes des attributs enregistrés.
     */
    protected function renderAttrInput(array $attr): string
    {
        $html = '';
        foreach ($attr as $key => $value) {
            if ($value === '' || (in_array($key, self::$attributesUnique) && empty($value))) {
                continue;
            }
            $html .= in_array($key, self::$attributesUnique)
                ? ' ' . $key
                : ' ' . htmlspecialchars($key) . '="' . htmlentities((string) $value) . '"';
        }

        return $html;
    }

    /**
     * Génère les inputs.
     *
     * @param string $key   Clé unique.
     * @param array  $input Paramètres du champ.
     *
     * @return string HTML
     */
    protected function renderInputs(string $key, array $input): string
    {
        $html = '';
        if (in_array($input[ 'type' ], self::$typeInputBasic)) {
            $html .= $this->renderInput($key, $input);
        } elseif ($input[ 'type' ] === 'label') {
            $html .= $this->renderLabel($key, $input);
        } elseif ($input[ 'type' ] === 'group') {
            $html .= $this->renderGroup($key, $input);
        } elseif ($input[ 'type' ] === 'legend') {
            $html .= $this->renderLegend($key, $input);
        } elseif ($input[ 'type' ] === 'select') {
            $html .= $this->renderSelect($key, $input);
        } elseif ($input[ 'type' ] === 'button') {
            $html .= $this->renderButton($key, $input);
        } elseif ($input[ 'type' ] === 'textarea') {
            $html .= $this->renderTextarea($key, $input);
        } elseif ($input[ 'type' ] === 'html') {
            $html .= $this->renderHtml($key, $input);
        }

        return $html;
    }

    /**
     * Génère un label.
     *
     * @return string HTML
     */
    protected function renderLabel(string $key, array $item): string
    {
        $label = $item[ 'attr' ][ ':label' ] ?? $item[ 'label' ];
        unset($item[ 'attr' ][ ':label' ]);

        $html = '';
        if (!empty($item[ 'attr' ][ 'data-tooltip' ])) {
            $html .= ' <i class="fa fa-info-circle"></i>';
        }
        if (!empty($item[ 'attr' ][ 'required' ]) || (isset($item[ 'attr' ][ 'for' ]) && $this->isRequired($item[ 'attr' ][ 'for' ]))) {
            $html .= '<span class="form-required">*</span>';
            unset($item[ 'attr' ][ 'required' ]);
        }

        return sprintf(
            '<label%s>%s%s</label>',
            $this->renderAttrInput($item[ 'attr' ]),
            $label,
            $html
        ) . self::EOL;
    }

    /**
     * Génère un champ.
     *
     * @return string HTML
     */
    protected function renderInput(string $key, array $item): string
    {
        return sprintf(
            '<input name="%s" type="%s"%s>',
            htmlspecialchars($key),
            $item[ 'type' ],
            $this->renderAttrInput($item[ 'attr' ])
        ) . self::EOL . $this->renderFeedback($key);
    }

    /**
     * Génère un groupe de champs.
     *
     * @return string HTML
     */
    protected function renderGroup(string $key, array $item): string
    {
        $tag = in_array($item[ 'balise' ], self::$baliseGroup)
            ? $item[ 'balise' ]
            : 'div';
        $tag = isset($item[ 'attr' ][ ':tag' ]) && in_array($item[ 'attr' ][ ':tag' ], self::$baliseGroup)
            ? $item[ 'attr' ][ ':tag' ]
            : $tag;
        unset($item[ 'attr' ][ ':tag' ]);

        return sprintf(
            '<%s%s>%s</%s>',
            $tag,
            $this->renderAttrInput($item[ 'attr' ]),
            self::EOL . $item[ 'form' ]->render(),
            $tag
        ) . self::EOL;
    }

    /**
     * Génère une balise legend.
     *
     * @return string HTML
     */
    protected function renderLegend(string $key, array $item): string
    {
        $legend = $item[ 'attr' ][ ':legend' ] ?? $item[ 'legend' ];
        unset($item[ 'attr' ][ ':legend' ]);

        return sprintf(
            '<legend%s>%s</legend>',
            $this->renderAttrInput($item[ 'attr' ]),
            htmlentities($legend)
        ) . self::EOL . $this->renderFeedback($key);
    }

    /**
     * Génère une balise textarea.
     *
     * @return string HTML
     */
    protected function renderTextarea(string $key, array $item): string
    {
        return sprintf(
            '<textarea name="%s"%s>%s</textarea>',
            htmlspecialchars($key),
            $this->renderAttrInput($item[ 'attr' ]),
            htmlentities($item[ 'content' ] ?? '')
        ) . self::EOL . $this->renderFeedback($key);
    }

    /**
     * Génère une balise button.
     *
     * @return string HTML
     */
    protected function renderButton(string $key, array $item): string
    {
        return sprintf(
            '<button name="%s" type="%s"%s>%s</button>',
            htmlspecialchars($key),
            $item[ 'type' ],
            $this->renderAttrInput($item[ 'attr' ]),
            htmlentities($item[ 'content' ] ?? '')
        ) . self::EOL . $this->renderFeedback($key);
    }

    /**
     * Génère une balise de selection.
     *
     * @return string HTML
     */
    protected function renderSelect(string $key, array $item): string
    {
        $select = $item[ 'attr' ][ ':selected' ] ?? '';
        unset($item[ 'attr' ][ ':selected' ]);

        return sprintf(
            '<select name="%s"%s>%s</select>',
            htmlspecialchars($key),
            $this->renderAttrInput($item[ 'attr' ]),
            self::EOL . $this->renderSelectOptionGroup($item[ 'options' ], $select)
        ) . self::EOL . $this->renderFeedback($key);
    }

    /**
     * Génère les balises options ou optgroup
     *
     * @param scalar $select
     */
    protected function renderSelectOptionGroup(array $options, $select): string
    {
        $html = '';

        foreach ($options as $option) {
            if (is_array($option[ 'value' ])) {
                $html .= sprintf(
                    '<optgroup label="%s">%s</optgroup>',
                    htmlentities($option[ 'label' ]),
                    $this->renderSelectOptionGroup($option[ 'value' ], $select)
                ) . self::EOL;

                continue;
            }
            $html .= $this->renderSelectOption($option, $select);
        }

        return $html;
    }

    /**
     * Génère une balise option
     *
     * @param scalar $select
     */
    protected function renderSelectOption(array $option, $select): string
    {
        $selected   = isset($option[ 'selected' ]) || ($select !== '' && $select === $option[ 'value' ])
            ? ' selected'
            : '';
        $attrOption = isset($option[ 'attr' ])
            ? $this->renderAttrInput($option[ 'attr' ])
            : '';

        return sprintf(
            '<option value="%s"%s%s>%s</option>',
            htmlspecialchars((string) $option[ 'value' ]),
            $attrOption,
            $selected,
            htmlentities($option[ 'label' ])
        ) . self::EOL;
    }

    /**
     * Génère une balise HTML.
     *
     * @return string HTML
     */
    protected function renderHtml(string $key, array $item): string
    {
        $content = isset($item[ 'attr' ][ ':content' ])
            ? $item[ 'attr' ][ ':content' ]
            : '';
        unset($item[ 'attr' ][ ':content' ]);

        return str_replace(
            [ ':attr', ':content' ],
            [ $this->renderAttrInput($item[ 'attr' ]), $content ],
            $item[ 'html' ]
        ) . self::EOL;
    }

    /**
     * Génère des messages d'erreur ou de succes.
     *
     * @return string HTML
     */
    protected function renderFeedback(string $key): string
    {
        $html = '';
        if (isset(self::$errors[ $key ])) {
            foreach (self::$errors[ $key ] as $msg) {
                $html .= '<div class="invalid-feedback">'
                    . htmlspecialchars($msg) . '</div>' . self::EOL;
            }
        } elseif (isset(self::$success[ $key ])) {
            foreach (self::$success[ $key ] as $msg) {
                $html .= '<div class="valid-feedback">'
                    . htmlspecialchars($msg) . '</div>' . self::EOL;
            }
        }

        return $html;
    }
}
