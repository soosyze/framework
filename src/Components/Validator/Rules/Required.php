<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Psr\Http\Message\UploadedFileInterface;
use Soosyze\Components\Validator\Rule;
use Soosyze\Components\Validator\RuleInputsInterface;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Required extends Rule implements RuleInputsInterface
{
    /**
     * Ensemble des champs.
     *
     * @var array
     */
    protected $inputs;

    /**
     * {@inheritdoc}
     *
     * @param array $inputs Ensemble des champs.
     */
    public function setInputs(array $inputs): void
    {
        $this->inputs = $inputs;
    }

    /**
     * Test si une valeur est requise.
     *
     * @param string     $key   Clé du test.
     * @param mixed      $value Valeur à tester.
     * @param mixed|null $args  Argument de test.
     * @param bool       $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        if (is_string($value) && mb_strlen(trim($value), 'UTF-8') === 0) {
            $this->addReturn($key, 'must', [ ':values' => $args ]);
        } elseif (is_array($value) && $value === []) {
            $this->addReturn($key, 'must', [ ':values' => $args ]);
        } elseif ($value instanceof UploadedFileInterface) {
            if ($value->getError() === UPLOAD_ERR_NO_FILE) {
                $this->addReturn($key, 'must', [ ':values' => $args ]);
            }
        }

        if ($this->hasErrors()) {
            $not
                    ? $this->stopPropagation()
                    : $this->stopImmediatePropagation();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return [
            'must' => 'The :label field is required.'
        ];
    }
}
