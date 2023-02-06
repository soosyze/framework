<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class RequiredWith extends Required
{
    /**
     * Test si une valeur est requise si un ensemble de champs est présent.
     *
     * @param string $key   Clé du test.
     * @param mixed  $value Valeur à tester.
     * @param mixed  $args  Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        if ($this->isOneValue()) {
            parent::test($key, $value, $args, $not);
        }
    }

    /**
     * Test si au moins une valeur n'est pas vide.
     *
     * @throws \InvalidArgumentException A field must be provided for the required with rule.
     */
    protected function isOneValue(): bool
    {
        if (empty($this->args)) {
            throw new \InvalidArgumentException('A field must be provided for the required with rule.');
        }
        if (!is_string($this->args)) {
            throw new \TypeError('The argument must be a string.');
        }
        $fields = explode(',', $this->args);
        foreach ($fields as $field) {
            if (!isset($this->inputs[ $field ])) {
                throw new \InvalidArgumentException(htmlspecialchars(
                    "The provided $field field does not exist."
                ));
            }

            $require = (new Required)
                ->hydrate('required', $field, null, true)
                ->execute($this->inputs[ $field ]);
            if (!$require->hasErrors()) {
                return true;
            }
        }

        return false;
    }
}
