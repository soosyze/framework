<?php

declare(strict_types=1);

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
     * @param string $key
     * @param array  $rules
     */
    protected function execute(string $key, array $rules): void
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
