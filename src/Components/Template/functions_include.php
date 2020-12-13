<?php

/**
 * Soosyze Framework https://soosyze.com
 *
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

/**
 * Si la valeur existe et non nul alors elle est renvoyée, sinon la valeur par défaut est renvoyée.
 *
 * @param mixed $var
 * @param mixed $default
 *
 * @return mixed
 */
function not_empty_or($var, $default = '')
{
    return empty($var)
        ? $default
        : $var;
}

/**
 * Si la condition est valide alors la première valeur est retournée sinon la seconde valeur est retournée.
 *
 * @param mixed $condition
 * @param mixed $value_true
 * @param mixed $value_false
 *
 * @return type
 */
function if_or($condition, $value_true, $value_false = '')
{
    return $condition
        ? $value_true
        : $value_false;
}
