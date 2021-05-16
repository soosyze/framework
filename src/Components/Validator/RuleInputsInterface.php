<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator;

/**
 * Si la règle peut posséder l'ensemble des valeurs.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
interface RuleInputsInterface
{
    /**
     * Ajoute des champs.
     *
     * @param array $inputs Ensemble des champs.
     *
     * @return void
     */
    public function setInputs(array $inputs): void;
}
