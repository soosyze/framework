<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Validator
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator;

/**
 * Filtre une valeur.
 *
 * @author Mathieu NOËL
 */
abstract class Filter extends Rule
{
    /**
     * Exécute le filtre.
     *
     * @param mixed $value Valeur à filtrer.
     *
     * @return $this
     */
    public function execute($value)
    {
        $this->value = $this->clean($this->keyRule, $value, $this->args);
    }

    /**
     * Défini le filtre.
     *
     * @param string $key   Identifiant de la valeur.
     * @param string $value Valeur à filtrer.
     * @param string $arg   Argument de filtre.
     */
    abstract protected function clean($key, $value, $arg);

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        throw new \BadMethodCallException;
    }

    /**
     * Défini le test.
     *
     * @param string $keyRule Clé du test.
     * @param string $value   Valeur à tester.
     * @param string $args    Argument de test.
     * @param bool   $not     Inverse le test.
     */
    protected function test($keyRule, $value, $args, $not = true)
    {
        throw new \BadMethodCallException;
    }
}
