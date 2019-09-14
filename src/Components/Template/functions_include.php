<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Validator2\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

/**
 * Si la valeur existe alors elle est renvoyée, sinon la valeur par défaut est renvoyée.
 *
 * @param mixed $var     Valeur testée.
 * @param mixed $default Valeur par défaut.
 *
 * @return mixed
 */
function isset_or(&$var, $default = '')
{
    return isset($var)
        ? $var
        : $default;
}
