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
class ToFloat extends \Soosyze\Components\Validator\Filter
{
    /**
     * Filtre une valeur avec la méthode htmlspecialchars.
     *
     * @param string $key   Identifiant de la valeur.
     * @param mixed  $value Valeur à filtrer.
     * @param string $arg   Argument de filtre.
     *
     * @throws \InvalidArgumentException The type must be validated before being filtered.
     *
     * @return float
     */
    protected function clean($key, $value, $arg)
    {
        if (($out = filter_var($value, FILTER_VALIDATE_FLOAT)) === false) {
            throw new \InvalidArgumentException('The type must be validated before being filtered.');
        }

        return $out;
    }
}
