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
 * Si la règle peut posséder l'ensemble des valeurs.
 *
 * @author Mathieu NOËL
 */
interface RuleInputsInterface
{
    /**
     * Ajoute toute les champs.
     *
     * @param array $inputs Ensemble des champs.
     */
    public function setInputs(array $inputs);
}
