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
abstract class Filter
{
    /**
     * Exécute le filtre de données.
     *
     * @param string $key   Identifiant de la valeur.
     * @param mixed  $value Valeur à filtrer.
     * @param string $arg   Argument de filtre.
     *
     * @return mixed $value Valeur à filtrer.
     */
    public function execute($key, $value, $arg)
    {
        return $this->clean($key, $value, $arg);
    }

    /**
     * Défini le filtre.
     *
     * @param string $key   Identifiant de la valeur.
     * @param string $value Valeur à filtrer.
     * @param string $arg   Argument de filtre.
     */
    abstract protected function clean($key, $value, $arg);
}
