<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Form;

/**
 * Créer des champs de formulaire.
 *
 * @see http://www.w3schools.com/html/html_forms.asp
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 *
 * @method form_button( string $key, array $attr = [] ): self
 * @method form_group( string $key, array $attr = [] ): self
 * @method form_html( string $key, array $attr = [] ): self
 * @method form_label( string $key, array $attr = [] ): self
 * @method form_legend( string $key, array $attr = [] ): self
 * @method form_select( string $key, array $attr = [] ): self
 * @method form_input( string $key, array $attr = [] ): self
 * @method form_textarea( string $key, array $attr = [] ): self
 * @method form_token( string $key, array $attr = [] ): self
 *
 * @method checkbox( string $key, array $attr = [] ): self
 * @method color( string $key, array $attr = [] ): self
 * @method date( string $key, array $attr = [] ): self
 * @method email( string $key, array $attr = [] ): self
 * @method file( string $key, array $attr = [] ): self
 * @method hidden( string $key, array $attr = [] ): self
 * @method image( string $key, array $attr = [] ): self
 * @method month( string $key, array $attr = [] ): self
 * @method password( string $key, array $attr = [] ): self
 * @method radio( string $key, array $attr = [] ): self
 * @method range( string $key, array $attr = [] ): self
 * @method reset( string $key, array $attr = [] ): self
 * @method search( string $key, array $attr = [] ): self
 * @method tel( string $key, array $attr = [] ): self
 * @method text( string $key, array $attr = [] ): self
 * @method time( string $key, array $attr = [] ): self
 * @method url( string $key, array $attr = [] ): self
 * @method week( string $key, array $attr = [] ): self
 */
class FormGroupBuilder
{
    use FormRenderTrait;

    protected const EOL = PHP_EOL;

    /**
     * Types des champs standards.
     *
     * @var string[]
     */
    protected static $typeInputBasic = [
        'checkbox',
        'color',
        'date',
        'email',
        'file',
        'hidden',
        'image',
        'month',
        'password',
        'radio',
        'range',
        'reset',
        'search',
        'tel',
        'text',
        'time',
        'url',
        'week'
    ];

    /**
     * Fonction de rendus.
     *
     * @var string[]
     */
    protected static $typeInputRender = [
        'form_button'   => 'renderButton',
        'form_group'    => 'renderGroup',
        'form_html'     => 'renderHtml',
        'form_label'    => 'renderLabel',
        'form_legend'   => 'renderLegend',
        'form_select'   => 'renderSelect',
        'form_input'    => 'renderInput',
        'form_textarea' => 'renderTextarea',
        'form_token'    => 'renderInput'
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
    protected static $errors = [];

    /**
     * Messages de réussites.
     *
     * @var array
     */
    protected static $success = [];

    /**
     * Génère au format html le formulaire.
     *
     * @return string HTML
     */
    public function __toString(): string
    {
        $html          = $this->render();
        self::$errors  = [];
        self::$success = [];

        return $html;
    }

    /**
     * Enregistre un input s'il est dans la liste des inputs standards.
     *
     * @param string $type Type de l'input.
     * @param array  $arg  [$name, id, array attr = null]
     *
     * @throws \BadMethodCallException Le type de champ d'existe pas.
     *
     * @return $this
     */
    public function __call(string $type, array $arg)
    {
        if (in_array($type, self::$typeInputBasic)) {
            array_unshift($arg, $type);

            return $this->inputBasic(...$arg);
        }
        if (isset(self::$typeInputRender[ $type ])) {
            $item = $this->getItem($arg[ 0 ]);
            $attr = $arg[ 1 ] ?? [];

            $item[ 'attr' ] = $this->mergeAttr($item[ 'attr' ], $attr);

            $method = self::$typeInputRender[ $type ];

            return $this->$method($arg[ 0 ], $item);
        }

        throw new \BadMethodCallException(htmlspecialchars("The $type type field does not exist"));
    }

    /**
     * Ajoute un ou plusieurs inputs avant un élément existant.
     *
     * @param string   $key      Clé unique.
     * @param callable $callback Fonction de création du sous-formulaire.
     *
     * @throws \OutOfBoundsException L'élément n'a pas été trouvé.
     *
     * @return $this
     */
    public function before(string $key, callable $callback): self
    {
        if ($this->addItem($key, $callback)) {
            return $this;
        }

        throw new \OutOfBoundsException(htmlspecialchars("The item $key was not found."));
    }

    /**
     * Ajoute un ou plusieurs inputs après un élément existant.
     *
     * @param string   $key      Clé unique.
     * @param callable $callback Fonction de création du sous-formulaire.
     *
     * @throws \OutOfBoundsException L'élément n'a pas été trouvé.
     *
     * @return $this
     */
    public function after(string $key, callable $callback): self
    {
        if ($this->addItem($key, $callback, true)) {
            return $this;
        }

        throw new \OutOfBoundsException(htmlspecialchars("The item $key was not found."));
    }

    /**
     * Place l'element en premier dans un élément.
     *
     * @param string   $key
     * @param callable $callback
     *
     * @throws \OutOfBoundsException
     *
     * @return $this
     */
    public function prepend(string $key, callable $callback): self
    {
        if ($this->addItemInto($key, $callback)) {
            return $this;
        }

        throw new \OutOfBoundsException(htmlspecialchars("The item $key was not found."));
    }

    /**
     * Place l'element en dernier dans un élément.
     *
     * @param string   $key
     * @param callable $callback
     *
     * @throws \OutOfBoundsException
     *
     * @return $this
     */
    public function append(string $key, callable $callback): self
    {
        if ($this->addItemInto($key, $callback, true)) {
            return $this;
        }

        throw new \OutOfBoundsException(htmlspecialchars("The item $key was not found."));
    }

    /**
     * Retourne les paramètres du formulaire.
     *
     * @return array
     */
    public function getForm(): array
    {
        return $this->form;
    }

    /**
     * Enregistre une balise HTML. Exemple :
     * <p:css:attr>:_content</p>
     * <img:css:attr />
     *
     * @param string $name Clé unique.
     * @param string $html La balise HTML à utiliser.
     * @param array  $attr Liste d'attributs.
     *
     * @return $this
     */
    public function html(string $name, string $html, array $attr = []): self
    {
        return $this->input($name, [
                'type' => 'html', 'html' => $html, 'attr' => $attr + [ 'id' => $name ]
        ]);
    }

    /**
     * Enregistre un groupe d'input.
     *
     * @param string   $name     Nom du groupe.
     * @param string   $balise   Type de balise (div|span|fieldset).
     * @param callable $callback Fonction de création du sous-formulaire.
     * @param array    $attr     Liste d'attributs.
     *
     * @return $this
     */
    public function group(
        string $name,
        string $balise,
        callable $callback,
        array $attr = []
    ): self {
        $subform = new FormGroupBuilder;
        $callback($subform);

        return $this->input($name, [
                'type'   => 'group',
                'form'   => $subform,
                'balise' => $balise,
                'attr'   => $attr
        ]);
    }

    /**
     * Enregistre un label.
     *
     * @param string          $name  Clé unique.
     * @param string|callable $label Texte à afficher ou sous formulaire.
     * @param array           $attr  Liste d'attributs.
     *
     * @return $this
     */
    public function label(string $name, $label, array $attr = []): self
    {
        if (!\is_string($label) && \is_callable($label)) {
            $subform = new FormGroupBuilder;
            $label($subform);
            $label   = $subform;
        }

        return $this->input($name, [
            'type' => 'label', 'label' => $label, 'attr' => $attr
        ]);
    }

    /**
     * Enregistre une legende.
     *
     * @param string $name   Clé unique.
     * @param string $legend Texte à afficher.
     * @param array  $attr   Liste d'attributs.
     *
     * @return $this
     */
    public function legend(string $name, string $legend, array $attr = []): self
    {
        return $this->input($name, [
            'type' => 'legend', 'legend' => $legend, 'attr' => $attr
        ]);
    }

    /**
     * Enregistre un champ numerique.
     *
     * @param string $name Clé unique.
     * @param array  $attr Liste d'attributs.
     *
     * @return $this
     */
    public function number(string $name, array $attr = []): self
    {
        $actions = !empty($attr[ ':actions' ]);
        unset($attr[ ':actions' ]);

        $this->input($name, [ 'type' => 'number', 'attr' => $attr + [ 'id' => $name ] ]);

        if ($actions) {
            $value = $attr[ 'value' ]
                ?? 0;
            $step  = $attr[ 'step' ]
                ?? 1;
            $min   = $attr[ 'min' ]
                ?? null;
            $max   = $attr[ 'max' ]
                ?? null;

            $this->html("$name-decrement", '<button:attr>:content</button>', [
                ':content'    => '<i class="fa fa-minus" aria-hidden="true"></i>',
                'class'       => 'btn input-number-decrement',
                'data-target' => "#$name",
                'disabled'    => ($min && $value - $step < $min),
                'type'        => 'button'
            ]);
            $this->html("$name-increment", '<button:attr>:content</button>', [
                ':content'    => '<i class="fa fa-plus" aria-hidden="true"></i>',
                'class'       => 'btn input-number-increment',
                'data-target' => "#$name",
                'disabled'    => ($max && $value + $step > $max),
                'type'        => 'button'
            ]);
        }

        return $this;
    }

    /**
     * Enregistre un textarea.
     *
     * @param string $name    Clé unique.
     * @param string $content Contenu du textarea.
     * @param array  $attr    Liste d'attributs.
     *
     * @return $this
     */
    public function textarea(
        string $name,
        ?string $content = '',
        array $attr = []
    ): self {
        return $this->input($name, [
            'type' => 'textarea', 'content' => $content, 'attr' => $attr + [ 'id' => $name ]
        ]);
    }

    /**
     * Enregistre un button.
     *
     * @param string $name    Clé unique.
     * @param string $content Contenu du button.
     * @param array  $attr    Liste d'attributs.
     *
     * @return $this
     */
    public function button(
        string $name,
        ?string $content = '',
        array $attr = []
    ): self {
        return $this->input($name, [
            'type' => 'button', 'content' => $content, 'attr' => $attr + [ 'id' => $name ]
        ]);
    }

    /**
     * Enregistre une datetime.
     *
     * @param string $name Clé unique.
     * @param array  $attr Liste d'attributs.
     *
     * @return $this
     */
    public function datetime(string $name, array $attr = []): self
    {
        return $this->input($name, [
            'type' => 'datetime-local', 'attr' => $attr + [ 'id' => $name ]
        ]);
    }

    /**
     * Enregistre une liste de sélection.
     *
     * @param string $name    Clé unique.
     * @param array  $options Liste d'options [ 'value'=> '', 'label'=> '', 'selected' => bool , 'attr' => [] ].
     * @param array  $attr    Liste d'attributs.
     *
     * @return $this
     */
    public function select(string $name, array $options = [], array $attr = []): self
    {
        return $this->input($name, [
            'type' => 'select', 'options' => $options, 'attr' => $attr + [ 'id' => $name ]
        ]);
    }

    /**
     * Enregistre un input standard.
     *
     * @param string $type Type d'input.
     * @param string $name Clé unique.
     * @param array  $attr Liste d'attributs.
     *
     * @return $this
     */
    public function inputBasic(string $type, string $name, array $attr = []): self
    {
        return $this->input($name, [
            'type' => $type, 'attr' => $attr + [ 'id' => $name ]
        ]);
    }

    /**
     * Enregistre un submit.
     *
     * @param string $name  Clé unique.
     * @param string $value Texte à afficher.
     * @param array  $attr  Liste d'attributs.
     *
     * @return $this
     */
    public function submit(string $name, string $value, array $attr = []): self
    {
        return $this->input($name, [
            'type' => 'submit', 'attr' => $attr + [ 'id' => $name, 'value' => $value ]
        ]);
    }

    /**
     * Enregistre un token pour protéger des failles CRSF.
     *
     * @param string $name Clé unique.
     *
     * @return $this
     */
    public function token(string $name): self
    {
        if (session_id() === '') {
            @session_start([
                    'cookie_httponly' => true,
                    'cookie_secure'   => true
            ]);
        }
        /* On génère un token unique. */
        $token = uniqid((string) rand(), true);

        /* Et on le stocke. */
        $_SESSION[ 'token' ][ $name ] = $token;

        /* On enregistre aussi le timestamp correspondant au moment de la création du token. */
        $_SESSION[ 'token_time' ][ $name ] = time();

        $this->input($name, [
            'type' => 'hidden',
            'attr' => [ 'value' => $token ]
        ]);

        return $this;
    }

    /**
     * Retourne le tableau d'erreurs.
     *
     * @codeCoverageIgnore getter
     *
     * @return array Les erreurs.
     */
    public function getErrors(): array
    {
        return self::$errors;
    }

    /**
     * Retourne le tableau des succès.
     *
     * @codeCoverageIgnore getter
     *
     * @return array Les succès.
     */
    public function getSucces(): array
    {
        return self::$success;
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
    public function setErrors(array $errs): self
    {
        self::$errors = $errs;

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
    public function addError(string $err): self
    {
        self::$errors[] = $err;

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
    public function addErrors(array $errs): self
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
    public function setSuccess(array $success): self
    {
        self::$success = $success;

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
    public function addSuccess($success): self
    {
        self::$success[] = $success;

        return $this;
    }

    /**
     * Ajoute à un élément du formulaire une liste d'attributs.
     *
     * @param string $key  Clé unique.
     * @param array  $attr Liste des attributs.
     *
     * @return $this
     */
    public function addAttr(string $key, array $attr): self
    {
        $this->addAttrRecurses($key, $attr);

        return $this;
    }

    /**
     * Ajoute à plusieurs éléments une liste d'attributs.
     *
     * @param array $keys Liste de clé.
     * @param array $attr Liste des attributs.
     *
     * @return $this
     */
    public function addAttrs(array $keys, array $attr): self
    {
        foreach ($keys as $key => $value) {
            if (\is_array($value)) {
                $this->addAttrsArray($key, $value, $attr);

                continue;
            }
            $this->addAttr($value, $attr);
        }

        return $this;
    }

    /**
     * Retourne un item du formulaire à partir de sa clé.
     *
     * @param string $key Clé unique.
     *
     * @throws \OutOfBoundsException L'élément n'a pas été trouvé.
     *
     * @return array Les données de l'élément.
     */
    public function getItem(string $key): array
    {
        if (($find = $this->searchItem($key)) !== null) {
            return $find;
        }

        throw new \OutOfBoundsException(htmlspecialchars("The item $key was not found."));
    }

    /**
     * Ajoute des attributs pour les champs multiples.
     *
     * @param string $key   Clé du champ multiple.
     * @param array  $value Liste des champs.
     * @param array  $attr  Attributs à ajouter.
     *
     * @return void
     */
    protected function addAttrsArray(string $key, array $value, array $attr = []): void
    {
        foreach ($value as $i => $data) {
            if (!\is_array($data)) {
                $this->addAttr($key . '[' . $data . ']', $attr);

                continue;
            }
            $this->addAttrsArray($key . '[' . $i . ']', $data, $attr);
        }
    }

    /**
     * Génère un sous formulaire sans les balises d'ouverture et de fermeture.
     *
     * @return string HTML
     */
    protected function render(): string
    {
        $html = '';
        foreach ($this->form as $key => $input) {
            $html .= $this->renderInputs((string) $key, $input);
        }

        return $html;
    }

    /**
     * Enregistre un input.
     *
     * @param string $name Clé unique.
     * @param array  $attr Options des champs et attributs de la balise.
     *
     * @return $this
     */
    protected function input(string $name, array $attr): self
    {
        /**
         * Si le for n'est pas précisé dans le label précédent
         * il devient automatiquement l'id de la balise courante.
         */
        $previous = end($this->form);
        if ($previous && $previous[ 'type' ] === 'label' && !isset($previous[ 'attr' ][ 'for' ]) && isset($attr[ 'attr' ][ 'id' ])) {
            $this->form[ key($this->form) ][ 'attr' ][ 'for' ] = $attr[ 'attr' ][ 'id' ];
        }
        $this->form[ $name ] = $attr;

        return $this;
    }

    /**
     * Fusionne 2 tableaux sans écrasement de données si l'un des 2 est vide.
     *
     * @param array $tab1
     * @param array $tab2
     *
     * @return array Fusion des 2 tableaux.
     */
    protected function mergeAttr(
        array $tab1 = [],
        array $tab2 = []
    ): array {
        if (!$tab1 && $tab2) {
            return $tab2;
        }
        if ($tab1 && !$tab2) {
            return $tab1;
        }
        if (!$tab1 && !$tab2) {
            return [];
        }
        $intersect = array_intersect_key($tab1, $tab2);
        if ($intersect !== []) {
            foreach ($intersect as $key => $value) {
                $tab2[ $key ] .= ' ' . $value;
            }
        }

        return array_merge($tab1, $tab2);
    }

    /**
     * Si une balise est requise.
     *
     * @param string $key Clé unique.
     *
     * @return bool
     */
    protected function isRequired(string $key): bool
    {
        return !empty($this->form[ $key ][ 'attr' ][ 'required' ]);
    }

    /**
     * Recherche récursive d'un élément du formulaire à partir de sa clé
     * et lui ajoute une liste des attributs.
     *
     * @param string $key  Clé unique.
     * @param array  $attr Liste des attributs à ajouter.
     *
     * @return bool
     */
    protected function addAttrRecurses(string $key, array $attr): bool
    {
        if (isset($this->form[ $key ])) {
            $this->form[ $key ][ 'attr' ] = $this->mergeAttr($this->form[ $key ][ 'attr' ], $attr);

            return true;
        }

        foreach ($this->form as $input) {
            if ($input[ 'type' ] !== 'group') {
                continue;
            }

            if ($input[ 'form' ]->addAttrRecurses($key, $attr)) {
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
    protected function searchItem(string $key): ?array
    {
        if (isset($this->form[ $key ])) {
            return $this->form[ $key ];
        }

        foreach ($this->form as $input) {
            if ($input[ 'type' ] !== 'group') {
                continue;
            }

            if (($subform = $input[ 'form' ]->searchItem($key)) !== null) {
                return $subform;
            }
        }

        return null;
    }

    /**
     * Ajoute un formulaire au début du formulaire courant.
     *
     * @param FormGroupBuilder $form
     *
     * @return void
     */
    protected function addFirst(FormGroupBuilder $form): void
    {
        $this->form = $form->getForm() + $this->form;
    }

    /**
     * Ajoute un formulaire à la fin du formulaire courant.
     *
     * @param FormGroupBuilder $form
     *
     * @return void
     */
    protected function addEnd(FormGroupBuilder $form): void
    {
        $this->form += $form->getForm();
    }

    /**
     * Fonction PHP array_slice() pour tableau associatif.
     *
     * @see http://php.net/manual/fr/function.array-slice.php
     *
     * @param array  $input       Tableau associatif.
     * @param string $key
     * @param array  $replacement
     * @param bool   $after       Si le tableau de remplacement doit être intègré après.
     *
     * @return void
     */
    private function arraySpliceAssoc(
        array &$input,
        string $key,
        array $replacement,
        bool $after
    ): void {
        /** @var int $offset */
        $offset = array_flip(array_keys($input))[$key];

        $input = array_slice($input, 0, $offset + ($after ? 1 : 0), true)
            + $replacement
            + array_slice($input, $offset, null, true);
    }

    /**
     * Ajoute un nouvel élément de formulaire avant ou après un élément existant.
     *
     * @param string   $key      Clé unique.
     * @param callable $callback Fonction de création du sous-formulaire.
     * @param bool     $after    Si l'item doit être placé après l'élément représenter par la clé.
     *
     * @return bool
     */
    private function addItem(
        string $key,
        callable $callback,
        bool $after = false
    ): bool {
        if (isset($this->form[ $key ])) {
            $subform = new FormGroupBuilder;
            $callback($subform);
            $this->arraySpliceAssoc($this->form, $key, $subform->getForm(), $after);

            return true;
        }

        foreach ($this->form as $input) {
            if ($input[ 'type' ] !== 'group') {
                continue;
            }

            if ($input[ 'form' ]->addItem($key, $callback, $after)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ajoute un nouvel élément de formulaire au début ou la fin d'un group existant.
     *
     * @param string   $key      Clé unique.
     * @param callable $callback Fonction de création du sous-formulaire.
     * @param bool     $after    Si l'item doit être placé après l'élément représenter par la clé.
     *
     * @return bool
     */
    private function addItemInto(
        string $key,
        callable $callback,
        bool $after = false
    ): bool {
        if (isset($this->form[ $key ][ 'form' ])) {
            $subform = new FormGroupBuilder;
            $callback($subform);
            $after
                    ? $this->form[ $key ][ 'form' ]->addEnd($subform)
                    : $this->form[ $key ][ 'form' ]->addFirst($subform);

            return true;
        }

        foreach ($this->form as $input) {
            if ($input[ 'type' ] !== 'group') {
                continue;
            }

            if ($input[ 'form' ]->addItemInto($key, $callback, $after)) {
                return true;
            }
        }

        return false;
    }
}
