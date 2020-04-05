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
class ToInt extends \Soosyze\Components\Validator\Filter
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
        return is_int(filter_var($value, FILTER_VALIDATE_INT))
            ? filter_var($value, FILTER_VALIDATE_INT)
            : $value;
    }
}
