<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Psr\Http\Message\UploadedFileInterface;
use Soosyze\Components\Validator\Rule;
use Soosyze\Components\Validator\RuleInputsInterface;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
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
        if ($value === '') {
            $this->addReturn($key, 'must');
        } elseif ($value instanceof UploadedFileInterface) {
            if ($value->getError() === UPLOAD_ERR_NO_FILE) {
                $this->addReturn($key, 'must');
            }
        }

        if ($this->hasErrors() && !$not) {
            $this->stopImmediatePropagation();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must' => 'La valeur de :label est requise.'
        ];
    }
}
