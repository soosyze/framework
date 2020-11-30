<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator;

/**
 * Filtre une valeur.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
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
        $this->value = $this->clean($this->name, $value, $this->args);

        return $this;
    }

    /**
     * Défini le filtre.
     *
     * @param string $key   Identifiant de la valeur.
     * @param mixed  $value Valeur à filtrer.
     * @param string $arg   Argument de filtre.
     *
     * @return mixed
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
