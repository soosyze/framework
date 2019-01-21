<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Form
 * @author Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Form;

/**
 * Créer un formulaire conforme aux spécificités de HTML5.
 *
 * @see http://www.w3schools.com/html/html_forms.asp
 *
 * @author Mathieu NOËL
 */
class FormBuilder
{
    /**
     * Attributs CSS.
     *
     * @var string[]
     */
    protected $attributesCss = [
        'id', 'class', 'style'
    ];

    /**
     * Balises autorisées pour les groupes de formulaire.
     *
     * @var string[]
     */
    protected $baliseGroup = [
        'div', 'span', 'fieldset'
    ];

    /**
     * Types des champs standards.
     *
     * @var string[]
     */
    protected $typeInputBasic = [
        'button',
        'checkbox',
        'color',
        'date',
        'datetime-local',
        'email',
        'file',
        'hidden',
        'image',
        'month',
        'number',
        'password',
        'radio',
        'range',
        'reset',
        'search',
        'submit',
        'tel',
        'text',
        'time',
        'url',
        'week',
    ];

    /**
     * Attributs du formulaire.
     *
     * @var array
     */
    protected $form = [];

    /**
     * Messages d'erreurs.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Messages de réussites.
     *
     * @var array
     */
    protected $success = [];

    /**
     * Déclare l'ouverture du formulaire.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->openForm($attributes);
    }

    /**
     * Enregistre un input s'il est dans la liste des inputs standards.
     *
     * @param string $type Type de l'input.
     * @param array $arg [$name, id, array attr = null]
     *
     * @return $this
     *
     * @throws \BadMethodCallException Le type de champ d'existe pas.
     */
    public function __call($type, $arg)
    {
        if (in_array($type, $this->typeInputBasic)) {
            array_unshift($arg, $type);

            return call_user_func_array([ $this, 'inputBasic' ], $arg);
        }

        throw new \BadMethodCallException(
            'The '
        . htmlspecialchars($type)
        . ' type field does not exist'
        );
    }
    
    /**
     * Ajoute un ou pluisieurs input avant un élément existant.
     *
     * @param string $key Clé unique.
     * @param callable $callback Fonction de création du sous-formulaire.
     *
     * @return $this
     *
     * @throws \Exception L'élément n'a pas été trouvé.
     */
    public function addBefore($key, callable $callback)
    {
        if ($this->addItem($key, $callback)) {
            return $this;
        }

        throw new \Exception('The item ' . htmlspecialchars($key) . ' was not found.');
    }

    /**
     * Ajoute un ou pluisieurs input après un élément existant.
     *
     * @param string $key Clé unique.
     * @param callable $callback Fonction de création du sous-formulaire.
     *
     * @return $this
     *
     * @throws \Exception L'élément n'a pas été trouvé.
     */
    public function addAfter($key, callable $callback)
    {
        if ($this->addItem($key, $callback, true)) {
            return $this;
        }

        throw new \Exception('The item ' . htmlspecialchars($key) . ' was not found.');
    }

    /**
     * Retourne les paramètres du formulaire.
     *
     * @return array
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Génère au format html le formulaire.
     *
     * @return string HTML
     */
    public function renderForm()
    {
        $html = $this->form_open();
        $html .= $this->renderSubForm();
        $html .= $this->form_close();

        return $html;
    }

    /**
     * Enregistre l'ouverture du formulaire.
     *
     * @param array|null $attr Attributs de la balise form.
     *
     * @return $this
     */
    public function openForm(array $attr = null)
    {
        $this->form[ 'open' ] = [ 'attr' => $attr, 'type' => 'open' ];

        return $this;
    }

    /**
     * Enregistre une balise HTML. Exemple :
     * <p:css:attr>:_content</p>
     * <img:css:attr />
     *
     * @param string $name Clé unique.
     * @param string $html La balise HTML à utiliser.
     * @param array $attr Liste d'attributs.
     *
     * @return $this
     */
    public function html($name, $html, array $attr = null)
    {
        return $this->input($name, [ 'type' => 'html', 'html' => $html, 'attr' => $attr ]);
    }

    /**
     * Enregistre un groupe d'input.
     *
     * @param string $name Nom du groupe.
     * @param string $balise Type de balise (div|span|fieldset).
     * @param callable $callback Fonction de création du sous-formulaire.
     * @param array|null $attr Liste d'attributs.
     *
     * @return $this
     */
    public function group($name, $balise, callable $callback, $attr = null)
    {
        $form  = new FormBuilder([]);
        call_user_func_array($callback, [ &$form ]);
        $group = $this->merge_attr([ 'balise' => $balise ], $attr);

        return $this->input($name, [ 'type' => 'group', 'subform' => $form, 'attr' => $group ]);
    }

    /**
     * Enregistre un label.
     *
     * @param string $name Clé unique.
     * @param string $label Texte à afficher.
     * @param array|null $attr Liste d'attributs.
     *
     * @return $this
     */
    public function label($name, $label, array $attr = null)
    {
        return $this->input($name, [ 'type' => 'label', 'label' => $label, 'attr' => $attr ]);
    }

    /**
     * Enregistre une legende.
     *
     * @param string $name Clé unique.
     * @param string $legend Texte à afficher.
     * @param array|null $attr Liste d'attributs.
     *
     * @return $this
     */
    public function legend($name, $legend, array $attr = null)
    {
        return $this->input($name, [ 'type' => 'legend', 'legend' => $legend, 'attr' => $attr ]);
    }

    /**
     * Enregistre un textarea.
     *
     * @param string $name Clé unique.
     * @param string $content Contenu du textarea.
     * @param array|null $attr Liste d'attributs.
     *
     * @return $this
     */
    public function textarea($name, $id, $content = '', array $attr = null)
    {
        $basic = $this->merge_attr([ 'id' => $id ], $attr);
        
        return $this->input($name, [ 'id' => $id, 'type' => 'textarea', 'content' => $content, 'attr' => $basic ]);
    }

    /**
     * Enregistre une liste de sélection.
     *
     * @param string $name Clé unique.
     * @param array $options Liste d'options [ 'value'=>'', 'label'=>'','selected' => 0|1 ].
     * @param array $attr Liste d'attributs.
     *
     * @return $this
     */
    public function select($name, $id, $options = [], array $attr = null)
    {
        $basic = $this->merge_attr([ 'id' => $id ], $attr);
        
        return $this->input($name, [ 'type' => 'select', 'options' => $options, 'attr' => $basic ]);
    }

    /**
     * Enregistre un input standard.
     *
     * @param string $type Type d'input.
     * @param string $name Clé unique.
     * @param string $id Selecteur CSS.
     * @param array|null $attr Liste d'attributs.
     *
     * @return $this
     */
    public function inputBasic($type, $name, $id, array $attr = null)
    {
        $basic = $this->merge_attr([ 'id' => $id ], $attr);

        return $this->input($name, [ 'type' => $type, 'attr' => $basic ]);
    }

    /**
     * Enregistre un submit.
     *
     * @param string $name Clé unique.
     * @param string $value Texte à afficher.
     * @param array|null $attr Liste d'attributs.
     *
     * @return $this
     */
    public function submit($name, $value, array $attr = null)
    {
        $basic = $this->merge_attr([ 'value' => $value ], $attr);

        return $this->input($name, [ 'type' => 'submit', 'attr' => $basic ]);
    }

    /**
     * Enregistre un token pour protéger des failles CRSF.
     *
     * @return $this
     */
    public function token()
    {
        if (session_id() == '') {
            @session_start([
                'cookie_httponly' => true,
                'cookie_secure' => true
            ]);
        }
        /* On génère un token totalement unique. */
        $token                    = uniqid(rand(), true);
        //Et on le stocke
        $_SESSION[ 'token' ]      = $token;
        /* On enregistre aussi le timestamp correspondant au moment de la création du token. */
        $_SESSION[ 'token_time' ] = time();

        $this->input('token', [
            'type' => 'hidden',
            'id'   => 'token',
            'attr' => [ 'value' => $token ]
        ]);

        return $this;
    }

    /**
     * Génère une balise formulaire ouvrante.
     *
     * @param array|null $attrAdd Liste des attributs additionnels.
     *
     * @return string HTML
     */
    public function form_open(array $attrAdd = null)
    {
        $attr = $this->merge_attr($this->form[ 'open' ][ 'attr' ], $attrAdd);

        return '<form'
            . $this->getAttributesInput($attr)
            . $this->getAttributesCSS($attr)
            . ">\r\n";
    }

    /**
     * Génère une balise form fermante.
     *
     * @return string HTML
     */
    public function form_close()
    {
        return "</form>\r\n";
    }

    /**
     * Génère une balise label.
     *
     * @param string $key Clé unique.
     * @param array|null $attrAdd Liste d'attributs additionnels.
     *
     * @return string HTML
     */
    public function form_label($key, array $attrAdd = null)
    {
        $item = $this->getItem($key);
        $attr = $this->merge_attr($item[ 'attr' ], $attrAdd);
        $label = isset($attr[ 'attr' ][ 'label' ])
            ? $attr[ 'attr' ][ 'label' ]
            : $item[ 'label' ];
        unset($attr[ 'attr' ][ 'label' ]);

        $html = '<label'
            . $this->getAttributesCSS($attr)
            . $this->getAttributesInput($attr) . '>'
            . $label;
        $html .= isset($attr[ 'for' ]) && $this->isRequired($attr[ 'for' ])
            ? '<span class="form-required">*</span>'
            : '';
        $html .= "</label>\r\n";

        return $html;
    }

    /**
     * Génère une balise legend.
     *
     * @param string $key Clé unique.
     * @param array|null $attrAdd Liste d'attributs additionnels.
     *
     * @return string HTML
     */
    public function form_legend($key, array $attrAdd = null)
    {
        $item = $this->getItem($key);
        $attr = $this->merge_attr($item[ 'attr' ], $attrAdd);
        $legend = isset($attr[ 'attr' ][ 'label' ])
            ? $attr[ 'attr' ][ 'legend' ]
            : $item[ 'legend' ];
        unset($attr[ 'attr' ][ 'legend' ]);

        return '<legend'
            . $this->getAttributesCSS($attr)
            . $this->getAttributesInput($attr) . '>'
            . $legend
            . "</legend>\r\n";
    }
    
    /**
     * Génère une balise input standard.
     *
     * @param string $key Clé unique.
     * @param array|null $attrAdd Liste des attributs additionnels.
     *
     * @return string HTML
     */
    public function form_input($key, array $attrAdd = null)
    {
        $item = $this->getItem($key);
        $attr = $this->merge_attr($item[ 'attr' ], $attrAdd);

        return '<input name="' . $key . '" type="' . $item[ 'type' ] . '"'
            . $this->getAttributesCSS($attr)
            . $this->getAttributesInput($attr)
            . ">\r\n";
    }

    /**
     * Génère une balise select.
     *
     * @param string $key Clé unique.
     * @param array|null $attrAdd Liste des attributs additionnels.
     *
     * @return string HTML
     */
    public function form_select($key, array $attrAdd = null)
    {
        $item = $this->getItem($key);
        $attr = $this->merge_attr($item[ 'attr' ], $attrAdd);

        $html = '<select name="' . $key . '"'
            . $this->getAttributesCSS($attr)
            . $this->getAttributesInput($attr)
            . ">\r\n";
        foreach ($item[ 'options' ] as $option) {
            $selected = isset($option[ 'selected' ])
                ? 'selected'
                : '';
            if (isset($attr[ 'selected' ]) && $attr[ 'selected' ] == $option[ 'value' ]) {
                $selected = 'selected';
            }

            $html .= '<option value="' . $option[ 'value' ] . '" ' . $selected . '>'
                . $option[ 'label' ]
                . "</option>\r\n";
        }
        $html .= "</select>\r\n";

        return $html;
    }

    /**
     * Génère une balise textarea.
     *
     * @param string $key Clé unique.
     * @param array|null $attrAdd Liste des attributs additionnels.
     *
     * @return string HTML
     */
    public function form_textarea($key, array $attrAdd = null)
    {
        $item = $this->getItem($key);
        $attr = $this->merge_attr($item[ 'attr' ], $attrAdd);

        return '<textarea name="'
            . $key . '"'
            . $this->getAttributesCSS($attr)
            . $this->getAttributesInput($attr) . '>'
            . $item[ 'content' ]
            . "</textarea>\r\n";
    }

    /**
     * Génère une balise group.
     *
     * @param string $key Clé unique.
     * @param array|null $attrAdd Liste des attributs additionnels.
     *
     * @return string HTML
     */
    public function form_group($key, array $attrAdd = null)
    {
        $item = $this->getItem($key);
        $attr = $this->merge_attr($item[ 'attr' ], $attrAdd);

        $balise = in_array($attr[ 'balise' ], $this->baliseGroup)
            ? $attr[ 'balise' ]
            : 'div';

        return '<' . $balise . $this->getAttributesCSS($attr) . ">\r\n"
            . $item[ 'subform' ]->renderSubForm()
            . '</' . $balise . ">\r\n";
    }

    /**
     * Génère une balise HTML.
     *
     * @param string $key Clé unique.
     * @param array|null $attrAdd Liste des attributs additionnels.
     *
     * @return string HTML
     */
    public function form_html($key, array $attrAdd = null)
    {
        $item = $this->getItem($key);
        $attr = $this->merge_attr($item[ 'attr' ], $attrAdd);
        if (isset($attr[ '_content' ])) {
            $content = $attr[ '_content' ];
            unset($attr[ '_content' ]);
        } else {
            $content = '';
        }

        return str_replace(
                [ ':css', ':attr', ':_content' ],
            [ $this->getAttributesCSS($attr),
                $this->getAttributesInput($attr), $content ],
            $item[ 'html' ]
            ) . "\r\n";
    }

    /**
     * Génère une balise input hidden pour le token.
     *
     * @return string HTML
     */
    public function form_token()
    {
        return $this->form_input('token');
    }

    /**
     * Retourne le tableau d'erreurs.
     *
     * @codeCoverageIgnore getter
     *
     * @return array Les erreurs.
     */
    public function form_errors()
    {
        return $this->errors;
    }

    /**
     * Retourne le tableau des succès.
     *
     * @codeCoverageIgnore getter
     *
     * @return array Les succès.
     */
    public function form_success()
    {
        return $this->success;
    }

    /**
     * Ajoute les erreurs.
     *
     * @codeCoverageIgnore setter
     *
     * @param array $errs
     *
     * @return $this
     */
    public function setErrors(array $errs)
    {
        $this->errors = $errs;

        return $this;
    }

    /**
     * Rajoute une erreur.
     *
     * @codeCoverageIgnore add
     *
     * @param string $err
     *
     * @return $this
     */
    public function addError($err)
    {
        $this->errors[] = $err;

        return $this;
    }

    /**
     * Rajoute plusieurs erreurs.
     *
     * @codeCoverageIgnore adds
     *
     * @param array $errs
     *
     * @return $this
     */
    public function addErrors(array $errs)
    {
        foreach ($errs as $err) {
            $this->addError($err);
        }

        return $this;
    }

    /**
     * Ajoute les success.
     *
     * @codeCoverageIgnore setter
     *
     * @param array $success
     *
     * @return $this
     */
    public function setSuccess(array $success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Rajoute un success.
     *
     * @codeCoverageIgnore add
     *
     * @param string $success
     *
     * @return $this
     */
    public function addSuccess($success)
    {
        $this->success[] = $success;

        return $this;
    }

    /**
     * Ajoute à un élément du formulaire une liste d'attributs.
     *
     * @param string $key Clé unique.
     * @param array $attr Liste des attributs.
     *
     * @return $this
     *
     * @throws \Exception L'élément n'a pas été trouvé.
     */
    public function addAttr($key, array $attr)
    {
        if ($this->addAttrRecurses($key, $attr)) {
            return $this;
        }

        throw new \Exception('The item ' . htmlspecialchars($key) . ' was not found.');
    }

    /**
     * Ajoute à plusieurs éléments une liste d'attributs.
     *
     * @param string $keys Liste de clé.
     * @param array $attr Liste des attributs.
     *
     * @return $this
     */
    public function addAttrs(array $keys, array $attr)
    {
        foreach ($keys as $key) {
            $this->addAttr($key, $attr);
        }

        return $this;
    }

    /**
     * Retourne un item du formulaire à partir de sa clé.
     *
     * @param string $key Clé unique.
     *
     * @return array Les données de l'élément.
     *
     * @throws \Exception L'élément n'a pas été trouvé.
     */
    public function getItem($key)
    {
        if (($find = $this->searchItem($key)) !== null) {
            return $find;
        }

        throw new \Exception('The item ' . htmlspecialchars($key) . ' was not found.');
    }

    /**
     * Génère un sous formulaire sans les balises d'ouverture et de fermeture.
     *
     * @return string HTML
     */
    protected function renderSubForm()
    {
        $html = "";
        foreach ($this->form as $key => $input) {
            $html .= $this->renderInput($key, $input);
        }

        return $html;
    }

    /**
     * Génère les inputs.
     *
     * @param string $key Clé unique.
     * @param array $input Paramètres du champ.
     *
     * @return string HTML
     */
    protected function renderInput($key, array $input)
    {
        $html = '';
        switch ($input[ 'type' ]) {
            case 'label':
                $html .= $this->form_label($key);

                break;
            case 'legend':
                $html .= $this->form_legend($key);

                break;
            case 'select':
                $html .= $this->form_select($key);

                break;
            case 'textarea':
                $html .= $this->form_textarea($key);

                break;
            case 'group':
                $html .= $this->form_group($key);

                break;
            case 'html':
                $html .= $this->form_html($key);

                break;
            default:
                if (in_array($input[ 'type' ], $this->typeInputBasic)) {
                    $html .= $this->form_input($key);
                }
        }

        return $html;
    }

    /**
     * Enregistre un input.
     *
     * @param string $name Clé unique.
     * @param array $attr Options des champs et attributs de la balise.
     *
     * @return $this
     */
    protected function input($name, array $attr)
    {
        /**
         * Si le for n'est pas précisé dans le label précédent
         * il devient automatiquement l'id de la balise courante.
         */
        $previous = end($this->form);
        if ($previous && $previous[ 'type' ] == 'label' && !isset($previous[ 'attr' ][ 'for' ]) && isset($attr[ 'attr' ][ 'id' ])) {
            $this->form[ key($this->form) ][ 'attr' ][ 'for' ] = $attr[ 'attr' ][ 'id' ];
        }
        $this->form[ $name ] = $attr;

        return $this;
    }

    /**
    * Met en forme les attributs CSS pour les balises.
    *
    * @param array $attr Listes des attributs enregistrés.
    *
    * @return string
    */
    protected function getAttributesCSS(array $attr)
    {
        $output = [];
        foreach ($attr as $key => $values) {
            if (in_array($key, $this->attributesCss) && $values !== '') {
                $output[] = $key . '="' . $values . '"';
            }
        }
        $implode = implode(' ', $output);

        return $implode
            ? " $implode"
            : '';
    }

    /**
     * Met en forme les attributs pour les balises inputs standards.
     *
     * @param array $attr Listes des attributs enregistrés.
     *
     * @return string
     */
    protected function getAttributesInput(array $attr)
    {
        $output = [];
        foreach ($attr as $key => $values) {
            if (in_array($key, ['checked', 'required'])) {
                if (!empty($values)) {
                    $output[] = $key;
                }
            } elseif (!in_array($key, $this->attributesCss) && $values !== '' && $key !== 'selected') {
                $output[] = $key . '="' . $values . '"';
            }
        }
        $implode = implode(' ', $output);

        return $implode
            ? " $implode"
            : '';
    }

    /**
     * Fusionne 2 tableaux sans écrasement de données si l'un des 2 est vide.
     *
     * @param array|null $tab1
     * @param array|null $tab2
     * @param bool $crushed
     *
     * @return array Fusion des 2 tableaux.
     */
    protected function merge_attr(
        array $tab1 = null,
        array $tab2 = null,
        $crushed = false
    ) {
        if ($tab1 == null && $tab2 != null) {
            return $tab2;
        } elseif ($tab1 != null && $tab2 == null) {
            return $tab1;
        } elseif ($tab1 != null && $tab2 != null) {
            $intersect = array_intersect_key($tab1, $tab2);
            if ($intersect && !$crushed) {
                foreach ($intersect as $key => $value) {
                    $tab2[ $key ] .= ' ' . $value;
                }
            }

            return array_merge($tab1, $tab2);
        } else {
            return [];
        }
    }

    /**
     * Si une balise est requise.
     *
     * @param string $key Clé unique.
     *
     * @return bool
     */
    protected function isRequired($key)
    {
        return !empty($this->form[ $key ][ 'attr' ][ 'required' ]);
    }

    /**
     * Recherche récursive d'un élément du formulaire à partir de sa clé
     * et lui ajoute une liste des attributs.
     *
     * @param string $key Clé unique.
     * @param array $attr Liste des attributs à ajouter.
     *
     * @return $this|bool
     */
    protected function addAttrRecurses($key, array $attr)
    {
        if (isset($this->form[ $key ])) {
            $this->form [ $key ][ 'attr' ] = $this->merge_attr($this->form[ $key ][ 'attr' ], $attr);

            return true;
        }

        foreach ($this->form as $input) {
            if ($input[ 'type' ] != 'group') {
                continue;
            }

            if ($input[ 'subform' ]->addAttrRecurses($key, $attr)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Recherche récursive d'un élément du formulaire à partir de sa clé.
     *
     * @param string $key Clé unique.
     *
     * @return array|null Les données de l'élément recherché.
     */
    protected function searchItem($key)
    {
        if (isset($this->form[ $key ])) {
            return $this->form[ $key ];
        }

        foreach ($this->form as $input) {
            if ($input[ 'type' ] != 'group') {
                continue;
            }

            if (($subform = $input[ 'subform' ]->searchItem($key)) !== null) {
                return $subform;
            }
        }

        return null;
    }
    
    /**
     * Fonction PHP array_slice() pour tableau associatif.
     *
     * @see http://php.net/manual/fr/function.array-slice.php
     *
     * @param array $input Tableau associatif.
     * @param int|string $offset
     * @param int|string $length
     * @param array $replacement
     * @param bool $after Si le tableau de remplacement doit être intègré après.
     */
    private function array_splice_assoc(
        array &$input,
        $offset,
        $length,
        array $replacement,
        $after = false
    ) {
        $key_indices = array_flip(array_keys($input));

        if (isset($input[ $offset ]) && is_string($offset)) {
            $offset = $key_indices[ $offset ];
        }
        if (isset($input[ $length ]) && is_string($length)) {
            $length = $key_indices[ $length ] - $offset;
        }

        $input = array_slice($input, 0, $offset + ($after ? 1 : 0), true)
            + $replacement
            + array_slice($input, $offset + $length, null, true);
    }
    
    /**
     * Ajoute un nouvel élément de formulaire avant ou après un élément existant.
     *
     * @param string $key Clé unique.
     * @param callable $callback Fonction de création du sous-formulaire.
     * @param bool $after Si l'item doit être placé après l'élément représenter par la clé.
     *
     * @return bool
     */
    private function addItem($key, callable $callback, $after = false)
    {
        if (isset($this->form[ $key ])) {
            $form = new FormBuilder([]);
            call_user_func_array($callback, [ &$form ]);
            $this->array_splice_assoc($this->form, $key, ($after ? $key : 0), $form->getForm(), $after);

            return true;
        }

        foreach ($this->form as $input) {
            if ($input[ 'type' ] != 'group') {
                continue;
            }

            if ($input[ 'subform' ]->addBefore($key, $callback)) {
                return true;
            }
        }

        return false;
    }
}
