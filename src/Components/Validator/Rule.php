<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator;

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
     * @var string
     */
    protected $args = '';

    /**
     * Inverse le test.
     *
     * @var type
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
     * @param string $arg      Argument de test.
     * @param bool   $not      Inverse le test.
     *
     * @return $this
     */
    public function hydrate($keyRule, $keyValue, $arg, $not = true)
    {
        $this->name = $keyRule;
        $this->key  = $keyValue;
        $this->args = $arg;
        $this->not  = $not;

        return $this;
    }

    /**
     * Retourne toutes les erreurs.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Si une erreur existe.
     *
     * @return bool
     */
    public function hasErrors()
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
    public function setMessages(array $messages = [])
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Modifie les attributs des messages d'erreur.
     *
     * @param array $attributs
     *
     * @return $this
     */
    public function setAttributs(array $attributs)
    {
        $this->attributes = $attributs;

        return $this;
    }

    /**
     * Ajoute un label au champ.
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
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
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Retourne le nom du test.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Stop les tests suivants.
     */
    public function stopPropagation()
    {
        $this->propogation = true;
    }
    
    /**
     * Stop les tests suivants immédiatement.
     */
    public function stopImmediatePropagation()
    {
        $this->immediatePropagation = true;
    }

    /**
     * Si les tests suivants sont stoppés.
     *
     * @return bool
     */
    public function isStop()
    {
        return $this->propogation;
    }
    
    /**
     * Si les tests suivants sont stoppés immédiatement.
     *
     * @return bool
     */
    public function isStopImmediate()
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
     * @param string $value   Valeur à tester.
     * @param string $args    Argument de test.
     * @param bool   $not     Inverse le test.
     */
    abstract protected function test($keyRule, $value, $args, $not);

    /**
     * Défini les messages de retours par défauts.
     *
     * @return string[]
     */
    abstract protected function messages();

    /**
     * Ajoute une valeur de retour formatées en cas d'erreur de validation.
     *
     * @param string   $keyRule    Clé du test.
     * @param string   $keyMessage Identifiant du message à formater avec la valeur de test.
     * @param string[] $attributs  Liste d'arguments de remplacements pour personnaliser le message.
     */
    protected function addReturn($keyRule, $keyMessage, array $attributs = [])
    {
        $args           = $this->overrideAttributes($attributs);
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
     * @throws \InvalidArgumentException The minimum value of between must be numeric.
     * @throws \InvalidArgumentException The maximum value of entry must be numeric.
     * @throws \InvalidArgumentException The minimum value must not be greater than the maximum value.
     *
     * @return numeric[] Tableau des valeurs min et max.
     */
    protected function getParamMinMax($arg)
    {
        $explode = explode(',', $arg);
        if (!isset($explode[ 0 ], $explode[ 1 ])) {
            throw new \InvalidArgumentException('Between values are invalid.');
        }

        $min = $explode[ 0 ];
        $max = $explode[ 1 ];

        if (!is_numeric($min)) {
            throw new \InvalidArgumentException('The minimum value of between must be numeric.');
        }
        if (!is_numeric($max)) {
            throw new \InvalidArgumentException('The maximum value of entry must be numeric.');
        }
        if ($min > $max) {
            throw new \InvalidArgumentException('The minimum value must not be greater than the maximum value.');
        }

        return [ 'min' => $min, 'max' => $max ];
    }

    /**
     * Personnaliser les attributs de retour.
     *
     * @param array $attributs
     *
     * @return array
     */
    private function overrideAttributes(array $attributs)
    {
        $attributs += [ ':label' => $this->label ];
        foreach ($attributs as $key => $value) {
            if (isset($this->attributes[$key])) {
                $attributs[$key] = call_user_func_array($this->attributes[$key], [$value]);
            }
        }

        return $attributs;
    }
}
