<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Form;

/**
 * Créer un formulaire conforme aux spécificités de HTML5.
 *
 * @see http://www.w3schools.com/html/html_forms.asp
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class FormBuilder extends FormGroupBuilder
{
    const HTTP_METHODS = [ 'DELETE', 'OPTION', 'PATCH', 'PUT' ];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * Déclare l'ouverture du formulaire.
     */
    public function __construct(array $attr = [])
    {
        if (!empty($attr[ 'method' ]) && in_array(strtoupper($attr[ 'method' ]), self::HTTP_METHODS)) {
            $this->hidden('__method', [ 'value' => $attr[ 'method' ] ]);
            $attr[ 'method' ] = 'post';
        }
        $this->openForm($attr);
    }

    /**
     * Le formulaire au format HTML.
     */
    public function __toString(): string
    {
        $html          = $this->form_open() . $this->render() . $this->form_close();
        self::$errors  = [];
        self::$success = [];

        return $html;
    }

    /**
     * Enregistre l'ouverture du formulaire.
     *
     * @param array $attr Attributs de la balise form.
     *
     * @return $this
     */
    public function openForm(array $attr = []): self
    {
        $this->form[ 'open' ] = [ 'type' => 'open', 'attr' => $attr ];

        return $this;
    }

    /**
     * Génère une balise form fermante.
     *
     * @return string HTML
     */
    public function form_close(): string
    {
        return '</form>' . self::EOL;
    }

    /**
     * Génère une balise formulaire ouvrante.
     *
     * @param array $attrAdd Liste des attributs additionnels.
     *
     * @return string HTML
     */
    public function form_open(array $attrAdd = []): string
    {
        $attr = $this->mergeAttr($this->form[ 'open' ][ 'attr' ], $attrAdd);

        return sprintf(
            '<form%s>',
            $this->renderAttrInput($attr)
        ) . self::EOL;
    }

    /**
     * Hydrate les valeurs du formulaire.
     *
     * @return $this
     */
    public function setValues(array $values)
    {
        $this->values = array_merge($this->values, $values);

        return $this;
    }
}
