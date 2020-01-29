<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
 */
class RequiredWithoutAll extends Required
{
    /**
     * Test si une valeur est requise si un ensemble de champs n'est pas présent.
     *
     * @param string $key   Clé du test.
     * @param mixed  $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        parent::test($key, $value, $arg, $not);
        if (!$this->isStopImmediate() && !$this->isAllVoidValue()) {
            $this->stopImmediatePropagation();
        }
    }

    /**
     * Test si toute les valeurs sont vide.
     *
     * @throws \InvalidArgumentException A field must be provided for the required with rule.
     *
     * @return bool
     */
    protected function isAllVoidValue()
    {
        if (empty($this->args)) {
            throw new \InvalidArgumentException('A field must be provided for the required with rule.');
        }
        $fields = explode(',', $this->args);
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($this->inputs[ $field ])) {
                throw new \InvalidArgumentException(htmlspecialchars(
                    "The provided $field field does not exist."
                ));
            }

            $require = (new Required)
                ->hydrate('required', $field, false, true)
                ->execute($this->inputs[ $field ]);
            $errors  += $require->getErrors();
        }

        return count($errors) == count($fields);
    }
}
