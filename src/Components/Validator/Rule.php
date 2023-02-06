<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator;

use Soosyze\Components\Validator\Comparators\MinMax;

/**
 * Valide une valeur.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
abstract class Rule
{
    /**
     * La valeur de test.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Clé du test.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Argument de test.
     *
     * @var mixed
     */
    protected $args = '';

    /**
     * Inverse le test.
     *
     * @var bool
     */
    protected $not = true;

    /**
     * Clé d'appel de la valeur.
     *
     * @var string
     */
    private $key = '';

    /**
     * Le label du champ.
     *
     * @var string
     */
    private $label = '';

    /**
     * Si la suite des tests doit être stoppée.
     *
     * @var bool
     */
    private $propogation = false;

    /**
     * Si la suite des tests doit être stoppée immédiatement (avant le retour d'erreur).
     *
     * @var bool
     */
    private $immediatePropagation = false;

    /**
     * Valeurs de retour.
     *
     * @var string[]
     */
    private $errors = [];

    /**
     * Messages de retours par défauts.
     *
     * @var string[]
     */
    private $messages = [];

    /**
     * Attributs des messages de retours personnalisés.
     *
     * @var array
     */
    private $attributes = [];

    /**
     * Exécute le test de validation.
     *
     * @param string $keyRule  Clé du test.
     * @param string $keyValue Identifiant de la valeur.
     * @param mixed  $args     Argument de test, peut-être une valeur d'un champ.
     * @param bool   $not      Inverse le test.
     *
     * @return $this
     */
    public function hydrate(
        string $keyRule,
        string $keyValue,
        $args,
        bool $not = true
    ): self {
        $this->name = $keyRule;
        $this->key  = $keyValue;
        $this->args = $args;
        $this->not  = $not;

        return $this;
    }

    /**
     * Retourne toutes les erreurs.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Si une erreur existe.
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Rempli les messages de retours par défauts.
     *
     * @param string[] $messages Messages de retours.
     *
     * @return $this
     */
    public function setMessages(array $messages = []): self
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Modifie les attributs des messages d'erreur.
     *
     * @return $this
     */
    public function setAttributs(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Ajoute un label au champ.
     *
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Exécute le test.
     *
     * @param mixed $value Valeur à tester.
     *
     * @return $this
     */
    public function execute($value)
    {
        $this->errors = [];
        $this->value  = $value;
        $this->test($this->name, $this->value, $this->args, $this->not);

        return $this;
    }

    /**
     * Retourne la clé unique de la valeur.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Retourne le nom du test.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Stop les tests suivants.
     */
    public function stopPropagation(): void
    {
        $this->propogation = true;
    }

    /**
     * Stop les tests suivants immédiatement.
     */
    public function stopImmediatePropagation(): void
    {
        $this->immediatePropagation = true;
    }

    /**
     * Si les tests suivants sont stoppés.
     */
    public function isStop(): bool
    {
        return $this->propogation;
    }

    /**
     * Si les tests suivants sont stoppés immédiatement.
     */
    public function isStopImmediate(): bool
    {
        return $this->immediatePropagation;
    }

    /**
     * Retourne la valeur.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Défini le test.
     *
     * @param string $keyRule Clé du test.
     * @param mixed  $value   Valeur à tester.
     * @param mixed  $args    Argument de test.
     * @param bool   $not     Inverse le test.
     */
    abstract protected function test(string $keyRule, $value, $args, bool $not): void;

    /**
     * Défini les messages de retours par défauts.
     *
     * @return string[]
     */
    abstract protected function messages(): array;

    /**
     * Ajoute une valeur de retour formatées en cas d'erreur de validation.
     *
     * @param string $keyRule    Clé du test.
     * @param string $keyMessage Identifiant du message à formater avec la valeur de test.
     * @param array  $attributes Liste d'arguments de remplacements pour personnaliser le message.
     */
    protected function addReturn(
        string $keyRule,
        string $keyMessage,
        array $attributes = []
    ): void {
        $args           = $this->overrideAttributes($attributes);
        $argsKey        = array_keys($args);
        $this->messages += $this->messages();

        $this->errors[ $keyRule ] = str_replace($argsKey, $args, $this->messages[ $keyMessage ]);
    }

    /**
     * Si la chaine de caractère d'entrée correspond à 2 valeurs numériques séparées
     * par une virgule et que la première valeur et inférieur à la seconde alors,
     * ont renvoie les 2 valeurs dans un tableau.
     *
     * @param string $arg Chaine de paramétre.
     *
     * @throws \InvalidArgumentException Between values are invalid.
     */
    protected function getParamMinMax(string $arg): MinMax
    {
        $explode = explode(',', $arg);
        if (!isset($explode[ 0 ], $explode[ 1 ])) {
            throw new \InvalidArgumentException('Between values are invalid.');
        }

        return MinMax::create($explode[ 0 ], $explode[ 1 ]);
    }

    /**
     * Personnaliser les attributs de retour.
     */
    private function overrideAttributes(array $attributes): array
    {
        $attributes += [ ':label' => $this->label ];
        foreach ($attributes as $key => $value) {
            if (isset($this->attributes[ $key ])) {
                $attributes[ $key ] = call_user_func_array(
                    $this->attributes[ $key ],
                    [ $value ]
                );
            }
        }

        return $attributes;
    }
}
