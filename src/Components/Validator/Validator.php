<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator;

/**
 * Valide des valeurs à partir de tests chaînés.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
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
        'alpha_num'               => 'Rules\AlphaNum',
        'alpha_num_text'          => 'Rules\AlphaNumText',
        'array'                   => 'Rules\ArrayType',
        'base64'                  => 'Rules\Base64',
        'between'                 => 'Rules\Between',
        'between_numeric'         => 'Rules\BetweenNumeric',
        'bool'                    => 'Rules\BoolType',
        'class_exists'            => 'Rules\ClassExists',
        'colorhex'                => 'Rules\ColorHex',
        'date'                    => 'Rules\Date',
        'date_after'              => 'Rules\DateAfter',
        'date_after_or_equal'     => 'Rules\DateAfterOrEqual',
        'date_before'             => 'Rules\DateBefore',
        'date_before_or_equal'    => 'Rules\DateBeforeOrEqual',
        'date_format'             => 'Rules\DateFormat',
        'dir'                     => 'Rules\Dir',
        'email'                   => 'Rules\Email',
        'equal'                   => 'Rules\Equal',
        'equal_strict'            => 'Rules\EqualStrict',
        'file'                    => 'Rules\File',
        'file_extensions'         => 'Rules\FileExtensions',
        'file_mimes'              => 'Rules\FileMimes',
        'file_mimetypes'          => 'Rules\FileMimetypes',
        'float'                   => 'Rules\FloatType',
        'fontawesome'             => 'Rules\FontAwesome',
        'image'                   => 'Rules\Image',
        'image_dimensions_height' => 'Rules\ImageDimensionsHeight',
        'image_dimensions_width'  => 'Rules\ImageDimensionsWidth',
        'inarray'                 => 'Rules\InArray',
        'int'                     => 'Rules\IntType',
        'instanceof'              => 'Rules\Instance',
        'ip'                      => 'Rules\Ip',
        'iterable'                => 'Rules\IterableType',
        'json'                    => 'Rules\Json',
        'max'                     => 'Rules\Max',
        'max_numeric'             => 'Rules\MaxNumeric',
        'min'                     => 'Rules\Min',
        'min_numeric'             => 'Rules\MinNumeric',
        'null'                    => 'Rules\NullValue',
        'numeric'                 => 'Rules\NumericType',
        'regex'                   => 'Rules\Regex',
        'required'                => 'Rules\Required',
        'required_with'           => 'Rules\RequiredWith',
        'required_with_all'       => 'Rules\RequiredWithAll',
        'required_without'        => 'Rules\RequiredWithout',
        'required_without_all'    => 'Rules\RequiredWithoutAll',
        'ressource'               => 'Rules\RessourceType',
        'slug'                    => 'Rules\Slug',
        'string'                  => 'Rules\StringType',
        'to_bool'                 => 'Filters\ToBool',
        'to_float'                => 'Filters\ToFloat',
        'to_htmlsc'               => 'Filters\ToHtmlsc',
        'to_int'                  => 'Filters\ToInt',
        'to_ltrim'                => 'Filters\ToLtrim',
        'to_rtrim'                => 'Filters\ToRtrim',
        'to_striptags'            => 'Filters\ToStripTags',
        'to_trim'                 => 'Filters\ToTrim',
        'timezone'                => 'Rules\Timezone',
        'token'                   => 'Rules\Token',
        'url'                     => 'Rules\Url',
        'uuid'                    => 'Rules\Uuid',
        'version'                 => 'Rules\Version'
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
     * Liste des labels.
     *
     * @var string[]
     */
    protected $labelCustom = [];

    /**
     * Tests personnalisés par l'utilisateur.
     *
     * @var Rule[]
     */
    protected static $testsCustom = [];

    /**
     * Messages de retours personnalisés global.
     *
     * @var string[]
     */
    protected static $messagesCustomGlobal = [];

    /**
     * Messages de retours personnalisés.
     *
     * @var string[]
     */
    protected $messagesCustom = [];

    /**
     * Attributs des messages de retours personnalisés.
     *
     * @var array
     */
    protected $attributesCustom = [];
    
    /**
     * Ajoute un test personnalisé.
     *
     * @param string $key  Clé du test.
     * @param Rule   $rule Function de test.
     *
     * @return $this
     */
    public static function addTest($key, $rule)
    {
        self::$testsCustom[ $key ] = $rule;

        return new static;
    }

    /**
     * Ajoute des messages globaux de retours personnalisés.
     *
     * @param string[] $messages
     *
     * @return $this
     */
    public static function setMessagesGlobal(array $messages)
    {
        self::$messagesCustomGlobal = $messages;

        return new static;
    }

    /**
     * Ajoute des messages de retours personnalisés.
     *
     * @param string[] $messages
     *
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->messagesCustom = $messages;

        return $this;
    }

    public function setAttributs(array $attributs)
    {
        $this->attributesCustom = $attributs;

        return $this;
    }

    /**
     * Ajoute un tableau associatif de "key_field" => "Label du champ".
     *
     * @param string[] $labels
     *
     * @return $this
     */
    public function setLabel(array $labels)
    {
        $this->labelCustom = $labels;

        return $this;
    }

    /**
     * Ajoute un champ à tester.
     *
     * @codeCoverageIgnore add
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
     * Rajoute un label de champ.
     *
     * @codeCoverageIgnore add
     *
     * @param type $key
     * @param type $label
     *
     * @return $this
     */
    public function addLabel($key, $label)
    {
        $this->labelCustom[ $key ] = $label;

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
        $out = [];
        foreach ($this->errors as $key => $error) {
            $out += $this->resucrsiveError($error, $key, false);
        }

        return array_keys($out);
    }

    /**
     * Retourn un champ.
     *
     * @codeCoverageIgnore getter
     *
     * @param string $key     Nom du champ.
     * @param mixed  $default Valeur de retour par défaut.
     *
     * @return array Valeur d'un champ.
     */
    public function getInput($key, $default = '')
    {
        return array_key_exists($key, $this->inputs) && $this->inputs[ $key ] !== ''
            ? $this->inputs[ $key ]
            : $default;
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
        $inputs = $this->inputs;
        if (($diff   = array_diff_key($this->inputs, $this->rules))) {
            foreach (array_keys($diff) as $key) {
                unset($inputs[ $key ]);
            }
        }

        return $inputs;
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
        $inputs  = [];
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

        return array_diff_key($this->getInputs(), array_flip($inputs));
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
        $out = [];
        foreach ($this->errors as $key => $error) {
            $out += $this->resucrsiveError($error, $key);
        }

        return $out;
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
     * Lance les tests
     *
     * @return bool Si le test à réussit.
     */
    public function isValid()
    {
        $this->errors = [];
        foreach ($this->rules as $key => $tests) {
            if (\is_string($tests)) {
                $rules = [];
                /* Construit les règles. */
                foreach (explode('|', $tests) as $test) {
                    $rule    = $this->parseRules($key, $test);
                    $rules[] = $this->valoriseRule($key, $rule);
                }
                $this->execute($key, $rules);
            } elseif (\is_array($tests)) {
                $rules = [];
                /* Construit les règles. */
                foreach ($tests as $rule) {
                    if (is_string($rule)) {
                        $rule    = $this->parseRules($key, $rule);
                    }
                    $rules[] = $this->valoriseRule($key, $rule);
                }
                $this->execute($key, $rules);
            } elseif ($tests instanceof Validator) {
                $tests->inputs = $this->inputs[ $key ];
                if (!$tests->isValid()) {
                    $this->errors[ $key ] = $tests->errors;
                }
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
     * @param array $errors
     * @param type  $strKey
     * @param type  $rule
     *
     * @return array
     */
    protected function resucrsiveError(array $errors, $strKey = '', $rule = true)
    {
        $out = [];
        foreach ($errors as $key => $error) {
            if (is_array($error)) {
                $out += $this->resucrsiveError($error, $strKey . '[' . $key . ']', $rule);

                continue;
            }
            if ($rule) {
                $out[ $strKey . '[' . $key . ']' ] = $error;
            } else {
                $out[ $strKey ] = $error;
            }
        }

        return $out;
    }

    /**
     * Exécute les règles sur un champ.
     *
     * @param string $key   La clé des tests
     * @param Rule[] $rules Les règles.
     */
    protected function execute($key, array $rules)
    {
        foreach ($rules as $rule) {
            $value = $this->getCorrectInput($key, $this->inputs);
            $rule->execute($value);
            if ($rule->isStopImmediate()) {
                break;
            }
            if (!$rule->hasErrors()) {
                $this->inputs[ $key ] = $rule->getValue();

                continue;
            }
            if (!isset($this->errors[ $key ])) {
                $this->errors[ $key ] = [];
            }
            $this->errors[ $key ] += $rule->getErrors();
            if ($rule->isStop()) {
                break;
            }
        }
    }

    /**
     * Si la clé d'un champ correspond à la clé d'une règle alors sa valeur est retournée.
     * Sinon retourne une chaine vide.
     *
     * @param string $key    Clé d'une règle.
     * @param array  $inputs Liste des champs.
     *
     * @return mixed|string
     */
    protected function getCorrectInput($key, array $inputs)
    {
        return \array_key_exists($key, $inputs)
            ? $inputs[ $key ]
            : '';
    }
    
    /**
     * Retourne le nom, l'argument et la négation de la règle
     *
     * @param string $rule Règle compléte.
     *
     * @return array
     */
    protected function getInfosRule($rule)
    {
        $exp  = explode(':', $rule, 2);
        /* Retire le caractère de négation de la fonction. */
        $name = $exp[ 0 ][ 0 ] === '!'
            ? substr($exp[ 0 ], 1)
            : $exp[ 0 ];
        $arg  = isset($exp[ 1 ])
            ? $exp[ 1 ]
            : false;

        /* Si l'argument fait référence à un autre champ. */
        if ($arg !== false && isset($arg[ 0 ]) && $arg[ 0 ] === '@') {
            $keyArg = substr($arg, 1);
            $arg    = $this->inputs[ $keyArg ];
        }

        return [ strtolower($name), $arg, $rule[ 0 ] !== '!' ];
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
        list($name, $arg, $not) = $this->getInfosRule($strRule);

        if (isset(self::$testsCustom[ $name ])) {
            $class = self::$testsCustom[ $name ];
        } elseif (isset($this->tests[ $name ])) {
            $class = __NAMESPACE__ . '\\' . $this->tests[ $name ];
        } else {
            throw new \BadMethodCallException(htmlspecialchars(
                "The $name function does not exist."
            ));
        }

        $label = isset($this->labelCustom[ $key ])
            ? $this->labelCustom[ $key ]
            : $key;

        return (new $class)->hydrate($name, $key, $arg, $not);
    }
    
    protected function valoriseRule($key, Rule $rule)
    {
        $label = isset($this->labelCustom[ $key ])
            ? $this->labelCustom[ $key ]
            : $key;

        $name = $rule->setLabel($label)->getName();

        if (isset($this->attributesCustom[ $key ][ $name ])) {
            $rule->setAttributs($this->attributesCustom[ $key ][ $name ]);
        }
        if (isset($this->messagesCustom[ $key ][ $name ])) {
            $rule->setMessages($this->messagesCustom[ $key ][ $name ]);
        } elseif (isset(self::$messagesCustomGlobal[ $name ])) {
            $rule->setMessages(self::$messagesCustomGlobal[ $name ]);
        }
        if ($rule instanceof RuleInputsInterface) {
            $rule->setInputs($this->inputs);
        }

        return $rule;
    }
}
