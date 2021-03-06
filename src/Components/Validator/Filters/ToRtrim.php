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
class ToRtrim extends \Soosyze\Components\Validator\Filter
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
     * @return string
     */
    protected function clean($key, $value, $arg)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('The type must be validated before being filtered.');
        }

        return rtrim($value, empty($arg)
            ? " \t\n\r\0\x0B"
            : $arg);
    }
}
