<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Validator
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator;

/**
 * Valide des valeurs à partir de tests chaînés.
 *
 * @author Mathieu NOËL
 */
class Validator
{
    /**
     * Règles de validations.
     *
     * @var string[]
     */
    protected $rules = [];

    /**
     * Liste des tests standards.
     *
     * @var string[]
     */
    protected $tests = [
        'accepted'                => 'Rules\Accepted',
        'alphanum'                => 'Rules\AlphaNum',
        'alphanumtext'            => 'Rules\AlphaNumText',
        'array'                   => 'Rules\ArrayR',
        'between'                 => 'Rules\Between',
        'bool'                    => 'Rules\BoolR',
        'colorhex'                => 'Rules\ColorHex',
        'date'                    => 'Rules\Date',
        'date_after'              => 'Rules\DateAfter',
        'date_after_or_equal'     => 'Rules\DateAfterOrEqual',
        'date_before'             => 'Rules\DateBefore',
        'date_before_or_equal'    => 'Rules\DateBeforeOrEqual',
        'date_format'             => 'Rules\DateFormat',
        'dir'                     => 'Rules\Dir',
        'equal'                   => 'Rules\Equal',
        'email'                   => 'Rules\Email',
        'file'                    => 'Rules\File',
        'file_extensions'         => 'Rules\FileExtensions',
        'file_mimes'              => 'Rules\FileMimes',
        'file_mimetypes'          => 'Rules\FileMimetypes',
        'float'                   => 'Rules\FloatR',
        'image'                   => 'Rules\Image',
        'image_dimensions_height' => 'Rules\ImageDimensionsHeight',
        'image_dimensions_width'  => 'Rules\ImageDimensionsWidth',
        'inarray'                 => 'Rules\InArray',
        'int'                     => 'Rules\IntR',
        'ip'                      => 'Rules\Ip',
        'json'                    => 'Rules\Json',
        'max'                     => 'Rules\Max',
        'min'                     => 'Rules\Min',
        'regex'                   => 'Rules\Regex',
        'required'                => 'Rules\Required',
        'required_with'           => 'Rules\Required',
        'required_without'        => 'Rules\Required',
        'slug'                    => 'Rules\Slug',
        'string'                  => 'Rules\StringR',
        'token'                   => 'Rules\Token',
        'url'                     => 'Rules\Url'
    ];

    /**
     * Liste des filtre pour les valeurs.
     *
     * @var string[]
     */
    protected $filters = [
        'htmlsc'    => 'Filters\Htmlsc',
        'striptags' => 'Filters\StripTags'
    ];

    /**
     * Champs à tester.
     *
     * @var array[]
     */
    protected $inputs = [];

    /**
     * Valeurs de retour.
     *
     * @var string[]
     */
    protected $errors = [];

    /**
     * Clé unique des champs.
     *
     * @var string[]
     */
    protected $key = [];

    /**
     * Tests personnalisés par l'utilisateur.
     *
     * @var Rule[]
     */
    protected static $testsCustom = [];

    /**
     * Messages de retours personnalisés.
     *
     * @var string[]
     */
    protected static $messagesCustom = [];

    /**
     * Ajoute un test personnalisé.
     *
     * @param string $key  Clé du test.
     * @param Rule   $rule Function de test.
     *
     * @return $this
     */
    public static function addTest($key, Rule $rule)
    {
        self::$testsCustom[ $key ] = $rule;

        return new static;
    }

    /**
     * Ajoute des messages de retours personnalisés.
     *
     * @param string[] $messages
     *
     * @return $this
     */
    public static function setMessages(array $messages)
    {
        self::$messagesCustom = $messages;

        return new static;
    }

    /**
     * Ajoute un champ à tester.
     *
     * @param string $key   Nom du champ.
     * @param mixed  $value Valeur du champ.
     *
     * @return $this
     */
    public function addInput($key, $value)
    {
        $this->inputs[ $key ] = $value;

        return $this;
    }

    /**
     * Rajoute une règle de validation.
     *
     * @codeCoverageIgnore add
     *
     * @param string $key  Nom de du champ.
     * @param string $rule Règles à suivre.
     *
     * @return $this
     */
    public function addRule($key, $rule)
    {
        $this->rules[ $key ] = $rule;

        return $this;
    }

    /**
     * Retourne une erreur à partir de son nom.
     *
     * @codeCoverageIgnore getter
     *
     * @param string $key Nom de l'erreur.
     *
     * @return string
     */
    public function getError($key)
    {
        return $this->errors[ $key ];
    }

    /**
     * Retourne toutes les erreurs.
     *
     * @codeCoverageIgnore getter
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Retourne la liste des noms de champ pour lesquels il y a une erreur.
     *
     * @return string[]
     */
    public function getKeyInputErrors()
    {
        return $this->key;
    }

    /**
     * Retourn un champ.
     *
     * @codeCoverageIgnore getter
     *
     * @param string $key Nom du champ.
     *
     * @return array Valeur d'un champ.
     */
    public function getInput($key)
    {
        return $this->inputs[ $key ];
    }

    /**
     * Retourne les champs.
     *
     * @codeCoverageIgnore getter
     *
     * @return array Valeur des champs.
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * Retourne les champs hors ceux précisés en paramètre.
     *
     * @codeCoverageIgnore getter
     *
     * @return array Valeur des champs.
     */
    public function getInputsWithout()
    {
        $without = func_get_args();
        foreach ($without as $value) {
            /* Dans le cas ou les colonnes sont normales. */
            if (!\is_array($value)) {
                $inputs[] = $value;

                continue;
            }
            /* Dans le cas ou les colonnes sont dans un tableau. */
            foreach ($value as $fields) {
                $inputs[] = $fields;
            }
        }

        return array_diff_key($this->inputs, array_flip($inputs));
    }

    /**
     * La liste de la concaténation des noms de champs et erreurs.
     *
     * @codeCoverageIgnore getter
     *
     * @return string[]
     */
    public function getKeyErrors()
    {
        return array_keys($this->errors);
    }

    /**
     * Si une erreur existe.
     *
     * @codeCoverageIgnore has
     *
     * @param string $key Nom de l'erreur.
     *
     * @return bool
     */
    public function hasError($key)
    {
        return isset($this->errors[ $key ]);
    }

    /**
     * Si il y a eu des erreurs.
     *
     * @codeCoverageIgnore has
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Si le champ existe.
     *
     * @codeCoverageIgnore has
     *
     * @param string $key Nom du champ.
     *
     * @return bool
     */
    public function hasInput($key)
    {
        return isset($this->inputs[ $key ]);
    }

    /**
     * Si le champ est requis.
     *
     * @codeCoverageIgnore is
     *
     * @param string $key Nom du champ.
     *
     * @return bool
     */
    public function isRequired($key)
    {
        return !$this->isNotRequired($key);
    }

    /**
     * Si le champ est requis à condition de la présence d'un ensemble d'autres champs.
     *
     * @codeCoverageIgnore is
     *
     * @param string $key Nom du champ.
     *
     * @return type
     */
    public function isRequiredWhith($key)
    {
        return strstr($this->rules[ $key ], 'required_with') && !$this->isRequiredWhithout($key);
    }

    /**
     * Si le champ est requis à condition de l'absence d'un ensemble d'autres champs.
     *
     * @codeCoverageIgnore is
     *
     * @param string $key Nom du champ.
     *
     * @return type
     */
    public function isRequiredWhithout($key)
    {
        return strstr($this->rules[ $key ], 'required_without');
    }

    /**
     * Lance les tests
     *
     * @return bool Si le test à réussit.
     */
    public function isValid()
    {
        $this->key    = [];
        $this->errors = [];
        $this->correctInputs();
        foreach ($this->rules as $key => $test) {
            /* Si la valeur est requise uniquement avec la présence de certains champs. */
            if ($this->isRequiredWhith($key) && $this->isOneVoidValue($key)) {
                continue;
            }
            /* Si la valeur est requise uniquement en l'absence de certains champs. */
            if ($this->isRequiredWhithout($key) && !$this->isAllVoidValue($key)) {
                continue;
            }
            /* Si la valeur n'est pas requise et vide. */
            if ($this->isNotRequired($key) && $this->isVoidValue($key)) {
                continue;
            }
            /* Pour chaque règle cherche les fonctions séparées par un pipe. */
            foreach (explode('|', $test) as $rule) {
                $this->parseRules($key, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Ajoute les champs à tester.
     *
     * @param array $fields Liste des champs.
     *
     * @return $this
     */
    public function setInputs(array $fields)
    {
        $this->inputs = $fields;

        return $this;
    }

    /**
     * Ajoute les règles de validation.
     *
     * @codeCoverageIgnore setter
     *
     * @param array $rules Règles de validation.
     *
     * @return $this
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Si la valeur n'est pas strictement requise.
     *
     * @param string $key Nom du champ.
     *
     * @return bool
     */
    protected function isNotRequired($key)
    {
        return strstr($this->rules[ $key ], '!required') && !strstr($this->rules[ $key ], '!required_');
    }

    /**
     * Retourne le nom de la règle à partir de sa composition complète.
     *
     * @param string $rule Règle compléte.
     *
     * @return string Nom de la règle.
     */
    protected function getRuleName($rule)
    {
        /* Retire le caractère de négation de la fonction. */
        $function = $rule[ 0 ] == '!'
            ? substr($rule, 1)
            : $rule;

        /* Sépare le nom de la fonction si elle a des arguments. */
        if (($name = strstr($function, ':', true)) !== false) {
            return strtolower($name);
        }

        return strtolower($function);
    }

    /**
     * Retourne l'argument de la règle à partir de sa composition complète.
     *
     * @param string $rule Règle compléte.
     *
     * @return string Argument de la règle.
     */
    protected function getRuleArgs($rule)
    {
        /* Si l'argument fait référence à un autre champ. */
        if (($arg = substr(strstr($rule, ':'), 1)) !== false && $arg[ 0 ] == '@') {
            $keyArg = substr($arg, 1);
            $arg    = $this->inputs[ $keyArg ];
        }

        return $arg;
    }

    /**
     * Analyse et exécute une règle de validation.
     *
     * @param string $key     Nom du champ.
     * @param string $strRule Règle de validation.
     *
     * @throws \BadMethodCallException The function does not exist.
     */
    protected function parseRules($key, $strRule)
    {
        $name = $this->getRuleName($strRule);
        $arg  = $this->getRuleArgs($strRule);

        if (isset(self::$testsCustom[ $name ])) {
            $rule = self::$testsCustom[ $name ];
        } elseif (isset($this->tests[ $name ])) {
            $class = __NAMESPACE__ . '\\' . $this->tests[ $name ];
            $rule  = new $class();
        } elseif (isset($this->filters[ $name ])) {
            $class                = __NAMESPACE__ . '\\' . $this->filters[ $name ];
            $filter               = new $class();
            $this->inputs[ $key ] = $filter->execute($key, $this->inputs[ $key ], $arg);

            return 0;
        } else {
            throw new \BadMethodCallException(htmlspecialchars(
                "The $name function does not exist."
            ));
        }

        if (isset(self::$messagesCustom[ $name ])) {
            $rule->setMessages(self::$messagesCustom[ $name ]);
        }

        $rule->execute(
            $name,
            $key,
            $this->inputs[ $key ],
            $arg,
            $strRule[ 0 ] != '!'
        );

        if ($rule->hasErrors()) {
            $this->key[]  = $key;
            $this->errors += $rule->getErrors();
        }
    }

    /**
     * Si la valeur est vide.
     *
     * @param string $key Nom du champ.
     *
     * @return bool
     */
    protected function isVoidValue($key)
    {
        $require = new Rules\Required;
        $require->execute('required', $key, $this->inputs[ $key ], false, true);

        return $require->hasErrors();
    }

    /**
     * Si une des références d'une règle est vide.
     *
     * @param string $key  Nom du champ.
     * @param string $rule Règle par défaut à utiliser cette méthode.
     *
     * @throws \InvalidArgumentException Le champ fourni n'existe pas.
     * @return bool
     */
    protected function isOneVoidValue($key, $rule = 'required_with')
    {
        $fields  = $this->getParamField($this->rules[ $key ], $rule);
        $require = new Rules\Required;

        foreach ($fields as $field) {
            if (!isset($this->inputs[ $field ])) {
                throw new \InvalidArgumentException(htmlspecialchars(
                    "The provided $field field does not exist."
                ));
            }
            $require->execute('required', $field, $this->inputs[ $field ], false, true);
            if ($require->hasErrors()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Si toutes les références d'une régle sont vides.
     *
     * @param string $key  Nom du champ.
     * @param string $rule Règle par défaut à utiliser cette méthode.
     *
     * @throws \InvalidArgumentException
     * @return bool
     */
    protected function isAllVoidValue($key, $rule = 'required_without')
    {
        $fields  = $this->getParamField($this->rules[ $key ], $rule);
        $require = new Rules\Required;
        $errors  = [];

        foreach ($fields as $field) {
            if (!isset($this->inputs[ $field ])) {
                throw new \InvalidArgumentException(htmlspecialchars(
                    "The provided $field field does not exist."
                ));
            }
            $require->execute('required', $field, $this->inputs[ $field ], false, true);
            $errors += $require->getErrors();
        }

        return count($errors) == count($fields);
    }

    /**
     * Retourne les paramètres d'une règle d'un ensemble de règles.
     *
     * @param string $rules Ensemble de règles.
     * @param string $rule  Règle recherchée.
     *
     * @throws \InvalidArgumentException Un champ doit être fourni pour la règle required_with.
     * @return array                     Paramètre de la règle.
     */
    protected function getParamField($rules, $rule)
    {
        preg_match("/$rule:([A-Za-z0-9-_,]*)/", $rules, $matches);
        if (empty($matches[ 1 ])) {
            throw new \InvalidArgumentException('A field must be provided for the required with rule.');
        }

        return explode(',', $matches[ 1 ]);
    }

    /**
     * Si les règles contiennes plus de champs que les valeurs reçues,
     * les valeurs se voient corrigées.
     */
    private function correctInputs()
    {
        if (($diff = array_diff_key($this->rules, $this->inputs))) {
            foreach (array_keys($diff) as $key) {
                $this->inputs[ $key ] = '';
            }
        }
    }
}
