<?php

declare(strict_types=1);

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
 *
 * @phpstan-type Error array<string, array<string, string>>
 * @phpstan-type Errors array<string, Error>
 */
class Validator
{
    /**
     * Règles de validations.
     *
     * @var array
     * @phpstan-var array<string, string|Validator>
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
        'is_file'                 => 'Rules\IsFile',
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
     * @var array
     */
    protected $inputs = [];

    /**
     * Valeurs de retour.
     *
     * @var array
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
     * Tests globaux personnalisés par l'utilisateur.
     *
     * @var string[]
     */
    protected static $testsCustomGlobal = [];

    /**
     * Tests personnalisés par l'utilisateur.
     *
     * @var Rule[]
     */
    protected $testsCustom = [];

    /**
     * Messages de retours personnalisés global.
     *
     * @var array
     */
    protected static $messagesCustomGlobal = [];

    /**
     * Messages de retours personnalisés.
     *
     * @var array
     */
    protected $messagesCustom = [];

    /**
     * Attributs des messages de retours personnalisés.
     *
     * @var array
     */
    protected $attributesCustom = [];

    /**
     * Ajoute un test global personnalisé.
     *
     * @param string $key  Clé du test.
     * @param string $rule Function de test.
     *
     * @return self
     */
    public static function addTestGlobal(string $key, string $rule): self
    {
        self::$testsCustomGlobal[ $key ] = $rule;

        return new self();
    }

    /**
     * Ajoute un test personnalisé.
     *
     * @param string $key  Clé du test.
     * @param Rule   $rule Function de test.
     *
     * @return $this
     */
    public function addTest(string $key, Rule $rule): self
    {
        $this->testsCustom[ $key ] = $rule;

        return $this;
    }

    /**
     * Ajoute des messages globaux de retours personnalisés.
     *
     * @param array $messages
     *
     * @return self
     */
    public static function setMessagesGlobal(array $messages): self
    {
        self::$messagesCustomGlobal = $messages;

        return new self();
    }

    /**
     * Ajoute des messages de retours personnalisés.
     *
     * @param array $messages
     *
     * @return $this
     */
    public function setMessages(array $messages): self
    {
        $this->messagesCustom = $messages;

        return $this;
    }

    /**
     * Ajoute un message de retours personnalisés.
     *
     * @param string $key
     * @param array  $message
     *
     * @return $this
     */
    public function addMessage(string $key, array $message): self
    {
        $this->messagesCustom[ $key ] = $message;

        return $this;
    }

    /**
     * Ajoute des attributs de retours personnalisés.
     *
     * @param array $attributs
     *
     * @return $this
     */
    public function setAttributs(array $attributs): self
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
    public function setLabels(array $labels): self
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
    public function addInput(string $key, $value): self
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
    public function addRule(string $key, string $rule): self
    {
        $this->rules[ $key ] = $rule;

        return $this;
    }

    /**
     * Rajoute un label de champ.
     *
     * @codeCoverageIgnore add
     *
     * @param string $key
     * @param string $label
     *
     * @return $this
     */
    public function addLabel(string $key, string $label): self
    {
        $this->labelCustom[ $key ] = $label;

        return $this;
    }

    /**
     * Rajoute des labels de champ.
     *
     * @codeCoverageIgnore add
     *
     * @param string[] $labels
     *
     * @return $this
     */
    public function addLabels(array $labels): self
    {
        $this->labelCustom += $labels;

        return $this;
    }

    /**
     * Retourne une erreur à partir du nom du champ.
     *
     * @codeCoverageIgnore getter
     *
     * @param string $key Nom du champ.
     *
     * @return array
     * @phpstan-return Error
     */
    public function getError(string $key): array
    {
        return $this->errors[ $key ];
    }

    /**
     * Retourne toutes les erreurs.
     *
     * @codeCoverageIgnore getter
     *
     * @return array
     * @phpstan-return Errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retourne la liste des noms de champ pour lesquels il y a une erreur.
     *
     * @return string[]
     */
    public function getKeyInputErrors(): array
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
     * @return mixed Valeur d'un champ.
     */
    public function getInput(string $key, $default = '')
    {
        return isset($this->inputs[ $key ]) && $this->inputs[ $key ] !== ''
            ? $this->inputs[ $key ]
            : $default;
    }

    /**
     * Retourne les champs.
     *
     * @return array Valeur des champs.
     */
    public function getInputs(): array
    {
        return array_intersect_key($this->inputs, $this->rules);
    }

    /**
     * Retourne les champs hors ceux précisés en paramètre.
     *
     * @return array Valeur des champs.
     */
    public function getInputsWithout(array $without = []): array
    {
        return array_diff_key($this->getInputs(), array_flip($without));
    }

    /**
     * Retourne les champs hors ceux précisés en paramètre et ceux de type objet.
     *
     * @return array Valeur des champs.
     */
    public function getInputsWithoutObject(array $without = []): array
    {
        $inputsWithout = $this->getInputsWithout($without);
        foreach ($inputsWithout as $key => $input) {
            if (is_object($input)) {
                unset($inputsWithout[ $key ]);
            }
        }

        return $inputsWithout;
    }

    /**
     * La liste de la concaténation des noms de champs et erreurs.
     *
     * @codeCoverageIgnore getter
     *
     * @return string[]
     */
    public function getKeyErrors(): array
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
    public function hasError(string $key): bool
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
    public function hasErrors(): bool
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
    public function hasInput(string $key): bool
    {
        return isset($this->inputs[ $key ]);
    }

    /**
     * Lance les tests
     *
     * @return bool Si le test à réussit.
     */
    public function isValid(): bool
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
    public function setInputs(array $fields): self
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
    public function setRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Parcours les erreurs.
     *
     * @param array  $errors
     * @param string $strKey
     * @param bool   $rule
     *
     * @return array
     */
    protected function resucrsiveError(
        array $errors,
        string $strKey = '',
        bool $rule = true
    ): array {
        $out = [];
        foreach ($errors as $key => $error) {
            if (is_array($error)) {
                $out += $this->resucrsiveError($error, $strKey . '[' . $key . ']', $rule);

                continue;
            }

            $out[ $rule
                ? $strKey . '[' . $key . ']'
                : $strKey
                ] = $error;
        }

        return $out;
    }

    /**
     * Exécute les règles sur un champ.
     *
     * @param string $key   La clé des tests
     * @param Rule[] $rules Les règles.
     *
     * @return void
     */
    protected function execute(string $key, array $rules): void
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
    protected function getCorrectInput(string $key, array $inputs)
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
     * @phpstan-return array{string, string, bool}
     */
    protected function getInfosRule(string $rule): array
    {
        $exp  = explode(':', $rule, 2);
        /* Retire le caractère de négation de la fonction. */
        $name = $exp[ 0 ][ 0 ] === '!'
            ? substr($exp[ 0 ], 1)
            : $exp[ 0 ];
        $arg  = $exp[ 1 ] ?? '';

        /* Si l'argument fait référence à un autre champ. */
        if ($arg && $arg[ 0 ] === '@') {
            $keyArg = substr($arg, 1);
            $arg    = $this->inputs[ $keyArg ];
        }

        return [ $name, $arg, $rule[ 0 ] !== '!' ];
    }

    /**
     * Analyse et exécute une règle de validation.
     *
     * @param string $key     Nom du champ.
     * @param string $strRule Règle de validation.
     *
     * @throws \BadMethodCallException The function does not exist.
     */
    protected function parseRules(string $key, string $strRule): Rule
    {
        [ $name, $arg, $not ] = $this->getInfosRule($strRule);

        if (isset($this->testsCustom[ $name ])) {
            $class = $this->testsCustom[ $name ];
        } elseif (isset(self::$testsCustomGlobal[ $name ])) {
            $class = self::$testsCustomGlobal[ $name ];
        } elseif (isset($this->tests[ $name ])) {
            $class = __NAMESPACE__ . '\\' . $this->tests[ $name ];
        } else {
            throw new \BadMethodCallException(htmlspecialchars(
                "The $name function does not exist."
            ));
        }

        /** @var Rule $class */
        return (new $class)->hydrate($name, $key, $arg, $not);
    }

    /**
     * Valorise la règle du label, attributs, messages personnalisés.
     *
     * @param string $key
     * @param Rule   $rule
     *
     * @return Rule
     */
    protected function valoriseRule(string $key, Rule $rule): Rule
    {
        $label = $this->labelCustom[ $key ]
            ?? $key;

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
