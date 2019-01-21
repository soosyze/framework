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
 * Valide une valeur.
 *
 * @author Mathieu NOËL
 */
abstract class Rule
{
    /**
     * Clé d'appel de la valeur.
     *
     * @var string
     */
    private $keyValue = '';

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
     * Exécute le test de validation.
     *
     * @param string $keyRule Clé du test.
     * @param string $keyValue Identifiant de la valeur.
     * @param mixed $value Valeur à tester.
     * @param string $arg Argument de test.
     * @param bool $not Inverse le test.
     *
     * @return $this
     */
    public function execute($keyRule, $keyValue, $value, $arg, $not)
    {
        $this->errors   = [];
        $this->keyValue = $keyValue;
        $this->value    = $value;
        $this->test($keyRule, $value, $arg, $not);

        return $this;
    }

    /**
     * Défini le test.
     *
     * @param string $key Clé du test.
     * @param string $value Valeur à tester.
     * @param string $arg Argument de test.
     * @param bool $not Inverse le test.
     */
    abstract protected function test($key, $value, $arg, $not = true);

    /**
     * Défini les messages de retours par défauts.
     *
     * @return string[]
     */
    abstract protected function messages();

    /**
     * Ajoute une valeur de retour formatées en cas d'erreur de validation.
     *
     * @param string $keyRule Clé du test.
     * @param string $keyMessage Identifiant du message à formater avec la valeur de test.
     * @param string[] $value Liste d'arguments de remplacements pour personnaliser le message.
     */
    protected function addReturn($keyRule, $keyMessage, array $value = [])
    {
        $key            = "$this->keyValue.$keyRule";
        $args           = array_merge([ ':label' => $this->keyValue ], $value);
        $argsKey        = array_keys($args);
        $this->messages = array_merge($this->messages(), $this->messages);

        $this->errors[ $key ] = str_replace($argsKey, $args, $this->messages[ $keyMessage ]);
    }

    /**
     * Si la chaine de caractère d'entrée correspond à 2 valeurs numériques séparées
     * par une virgule et que la première valeur et inférieur à la seconde alors,
     * ont renvoie les 2 valeurs dans un tableau.
     *
     * @param string $arg Chaine de paramétre.
     *
     * @return numeric[] Tableau des valeurs min et max.
     *
     * @throws \InvalidArgumentException Between values are invalid.
     * @throws \InvalidArgumentException The minimum value of between must be numeric.
     * @throws \InvalidArgumentException The maximum value of entry must be numeric.
     * @throws \InvalidArgumentException The minimum value must not be greater than the maximum value.
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
        } elseif (!is_numeric($max)) {
            throw new \InvalidArgumentException('The maximum value of entry must be numeric.');
        } elseif ($min > $max) {
            throw new \InvalidArgumentException('The minimum value must not be greater than the maximum value.');
        }

        return [ 'min' => $min, 'max' => $max ];
    }
}
