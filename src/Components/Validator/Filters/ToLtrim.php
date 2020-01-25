<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Validator
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Filters;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
 */
class ToLtrim extends \Soosyze\Components\Validator\Filter
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
        return ltrim($value, is_string($arg)
            ? $arg
            : " \t\n\r\0\x0B");
    }
}
