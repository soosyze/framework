<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator;

/**
 * Valide des valeurs à partir de tests chaînés.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class ValidatorIterator extends Validator
{
    /**
     * {@inheritdoc}
     *
     * @param type  $key
     * @param array $rules
     */
    protected function execute($key, array $rules)
    {
        foreach ($rules as $rule) {
            foreach ($this->inputs as $i => $input) {
                $value = $this->getCorrectInput($key, $input);
                $rule->execute($value);
                if ($rule->isStopImmediate()) {
                    break;
                }
                if ($rule->hasErrors()) {
                    if (!isset($this->errors[ $i ][ $key ])) {
                        $this->errors[ $i ][ $key ] = [];
                    }
                    $this->errors[ $i ][ $key ] += $rule->getErrors();
                }
                $this->inputs[ $i ][ $key ] = $rule->getValue();
            }
        }
    }
}
