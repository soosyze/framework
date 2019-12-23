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
class RequiredWith extends Required
{
    /**
     * Test si une valeur est requise si un ensemble de champs est présent.
     *
     * @param string $key   Clé du test.
     * @param mixed  $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        parent::test($key, $value, $arg, $not);
        if ($this->isOneVoidValue()) {
            $this->stopPropagation();
        }
    }

    /**
     * Test si au moins une valeur vide.
     *
     * @throws \InvalidArgumentException A field must be provided for the required with rule.
     *
     * @return bool
     */
    protected function isOneVoidValue()
    {
        if (empty($this->args)) {
            throw new \InvalidArgumentException('A field must be provided for the required with rule.');
        }
        $fields = explode(',', $this->args);
        foreach ($fields as $field) {
            if (!isset($this->inputs[ $field ])) {
                throw new \InvalidArgumentException(htmlspecialchars(
                    "The provided $field field does not exist."
                ));
            }

            $require = (new Required)
                ->hydrate('required', $field, false, true)
                ->execute($this->inputs[ $field ]);
            if ($require->hasErrors()) {
                return true;
            }
        }

        return false;
    }
}
