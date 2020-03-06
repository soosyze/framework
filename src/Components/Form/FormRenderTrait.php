<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Form
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
     *
     * @return string
     */
    protected function renderAttrInput(array $attr)
    {
        $html = '';
        foreach ($attr as $key => $value) {
            if ($value === '' || (in_array($key, self::$attributesUnique) && empty($value))) {
                continue;
            }
            $html .= in_array($key, self::$attributesUnique)
                ? ' ' . $key
                : ' ' . htmlspecialchars($key) . '="' . htmlentities($value) . '"';
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
    protected function renderInputs($key, array $input)
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
     * @param string $key
     * @param array  $item
     *
     * @return string HTML
     */
    protected function renderLabel($key, array $item)
    {
        $label = isset($item[ 'attr' ][ 'label' ])
            ? $item[ 'attr' ][ 'label' ]
            : $item[ 'label' ];
        unset($item[ 'attr' ][ 'label' ]);

        $html = '';
        if (!empty($item[ 'attr' ][ 'data-tooltip' ])) {
            $html .= ' <i class="fa fa-info-circle"></i>';
        }
        if (!empty($item[ 'attr' ][ 'required' ]) || (isset($item[ 'attr' ][ 'for' ]) && $this->isRequired($item[ 'attr' ][ 'for' ]))) {
            $html .= '<span class="form-required">*</span>';
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
     * @param string $key
     * @param array  $item
     *
     * @return string HTML
     */
    protected function renderInput($key, array $item)
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
     * @param string $key
     * @param array  $item
     *
     * @return string HTML
     */
    protected function renderGroup($key, array $item)
    {
        $balise = in_array($item[ 'balise' ], self::$baliseGroup)
            ? $item[ 'balise' ]
            : 'div';
        $balise = isset($item[ 'attr' ][ 'balise' ]) && in_array($item[ 'attr' ][ 'balise' ], self::$baliseGroup)
            ? $item[ 'attr' ][ 'balise' ]
            : $balise;
        unset($item[ 'attr' ][ 'balise' ]);

        return sprintf(
            '<%s%s>%s</%s>',
            $balise,
            $this->renderAttrInput($item[ 'attr' ]),
            self::EOL . $item[ 'form' ]->render(),
            $balise
        ) . self::EOL;
    }

    /**
     * Génère une balise legend.
     *
     * @param string $key
     * @param array  $item
     *
     * @return string HTML
     */
    protected function renderLegend($key, array $item)
    {
        $legend = isset($item[ 'attr' ][ 'label' ])
            ? $item[ 'attr' ][ 'legend' ]
            : $item[ 'legend' ];
        unset($item[ 'attr' ][ 'legend' ]);

        return sprintf(
            '<legend%s>%s</legend>',
            $this->renderAttrInput($item[ 'attr' ]),
            htmlentities($legend)
        ) . self::EOL . $this->renderFeedback($key);
    }

    /**
     * Génère une balise textarea.
     *
     * @param string $key
     * @param array  $item
     *
     * @return string HTML
     */
    protected function renderTextarea($key, array $item)
    {
        return sprintf(
            '<textarea name="%s"%s>%s</textarea>',
            htmlspecialchars($key),
            $this->renderAttrInput($item[ 'attr' ]),
            htmlentities($item[ 'content' ])
        ) . self::EOL . $this->renderFeedback($key);
    }

    /**
     * Génère une balise de selection.
     *
     * @param string $key
     * @param array  $item
     *
     * @return string HTML
     */
    protected function renderSelect($key, array $item)
    {
        $select = isset($item[ 'attr' ][ 'selected' ])
            ? $item[ 'attr' ][ 'selected' ]
            : '';
        unset($item[ 'attr' ][ 'selected' ]);
        $html   = '';
        foreach ($item[ 'options' ] as $option) {
            $selected = isset($option[ 'selected' ]) || ($select !== '' && $select === $option[ 'value' ])
                ? 'selected'
                : '';

            $html .= sprintf(
                '<option value="%s" %s>%s</option>',
                htmlspecialchars($option[ 'value' ]),
                $selected,
                htmlentities($option[ 'label' ])
            ) . self::EOL;
        }

        return sprintf(
            '<select name="%s"%s>%s</select>',
            htmlspecialchars($key),
            $this->renderAttrInput($item[ 'attr' ]),
            self::EOL . $html
        ) . self::EOL . $this->renderFeedback($key);
    }

    /**
     * Génère une balise HTML.
     *
     * @param string $key
     * @param array  $item
     *
     * @return string HTML
     */
    protected function renderHtml($key, array $item)
    {
        $content = isset($item[ 'attr' ][ '_content' ])
            ? $item[ 'attr' ][ '_content' ]
            : '';
        unset($item[ 'attr' ][ '_content' ]);

        return str_replace(
            [ ':attr', ':_content' ],
            [ $this->renderAttrInput($item[ 'attr' ]), $content ],
            $item[ 'html' ]
        ) . self::EOL;
    }

    /**
     * Génère des messages d'erreur ou de succes.
     *
     * @param string $key
     *
     * @return string HTML
     */
    protected function renderFeedback($key)
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
