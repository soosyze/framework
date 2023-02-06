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
class ToBool extends \Soosyze\Components\Validator\Filter
{
    /**
     * Filtre une valeur avec la méthode htmlspecialchars.
     *
     * @param string $key   Identifiant de la valeur.
     * @param mixed  $value Valeur à filtrer.
     * @param mixed  $args  Argument de filtre.
     *
     * @throws \InvalidArgumentException The type must be validated before being filtered.
     */
    protected function clean(string $key, $value, $args): bool
    {
        if (!$this->isBool($value)) {
            throw new \InvalidArgumentException('The type must be validated before being filtered.');
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Si la variable est de type ou valeur boolean.
     *
     * @see https://www.php.net/ChangeLog-5.php#PHP_5_4 (5.4.8)
     * Fixed bug #49510 (Boolean validation fails with FILTER_NULL_ON_FAILURE with empty string or false.)
     *
     * @param mixed $var
     */
    protected function isBool($var): bool
    {
        return filter_var($var, FILTER_VALIDATE_BOOLEAN) || filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null || $var === false;
    }
}
