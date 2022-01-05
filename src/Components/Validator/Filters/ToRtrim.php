<?php

declare(strict_types=1);

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
     * @param mixed  $args  Argument de filtre.
     *
     * @throws \InvalidArgumentException The type must be validated before being filtered.
     *
     * @return string
     */
    protected function clean(string $key, $value, $args): string
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('The type must be validated before being filtered.');
        }
        if (!is_string($args)) {
            throw new \InvalidArgumentException(
                sprintf('The argument must be of type string: %s given', gettype($args))
            );
        }

        return rtrim($value, $args === '' ? " \t\n\r\0\x0B" : $args);
    }
}
