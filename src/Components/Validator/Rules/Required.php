<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Psr\Http\Message\UploadedFileInterface;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
 */
class Required extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si une valeur est requise.
     *
     * @param string $key   Clé du test.
     * @param mixed  $value Valeur à tester.
     * @param string $arg   Argument de test.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        if ($value == '') {
            $this->addReturn($key, 'must');
        } elseif ($value instanceof UploadedFileInterface) {
            if ($value->getError() === UPLOAD_ERR_NO_FILE) {
                $this->addReturn($key, 'must');
            }
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
