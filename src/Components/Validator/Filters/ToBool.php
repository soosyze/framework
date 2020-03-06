<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Filters;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class ToBool extends \Soosyze\Components\Validator\Filter
{
    /**
     * Filtre une valeur avec la méthode htmlspecialchars.
     *
     * @param string $key   Identifiant de la valeur.
     * @param string $value Valeur à filtrer.
     * @param string $arg   Argument de filtre.
     *
     * @throws \InvalidArgumentException La valeur time n'est pas numérique.
     */
    protected function clean($key, $value, $arg)
    {
        return $this->isBool($value)
            ? filter_var($value, FILTER_VALIDATE_BOOLEAN)
            : $value;
    }

    /**
     * Si la variable est de type ou valeur boolean.
     *
     * @param mixed $var
     *
     * @return bool
     */
    protected function isBool($var)
    {
        return filter_var($var, FILTER_VALIDATE_BOOLEAN) || filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null || $var === false || $var === '';
    }
}
