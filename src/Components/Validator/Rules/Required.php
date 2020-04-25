<?php

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
    public function setInputs(array $inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * Test si une valeur est requise.
     *
     * @param string $key   Clé du test.
     * @param mixed  $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        if (is_string($value) && mb_strlen(trim($value), 'UTF-8') === 0) {
            $this->addReturn($key, 'must', [ ':values' => $arg ]);
        } elseif (is_array($value) && count($value) === 0) {
            $this->addReturn($key, 'must', [ ':values' => $arg ]);
        } elseif ($value instanceof UploadedFileInterface) {
            if ($value->getError() === UPLOAD_ERR_NO_FILE) {
                $this->addReturn($key, 'must', [ ':values' => $arg ]);
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
    protected function messages()
    {
        return [
            'must' => 'The :label field is required.'
        ];
    }
}
