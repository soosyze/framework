<?php

/**
 * Soosyze Framework http://soosyze.com
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
class FileMimetypes extends File
{

    /**
     * Test si un fichier possède l'un des mimetypes fournit dans les arguments.
     *
     * @param string $key Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param string $arg Liste de mimetypes.
     * @param bool $not Inverse le test.
     *
     * @return int 1 erreur de fichier.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        parent::test('file', $value, false);

        if ($this->hasErrors()) {
            return 1;
        }

        $mines = explode(',', $arg);
        $info  = $this->getMime($value);

        if (!in_array($info, $mines) && $not) {
            $this->addReturn('', 'must');
        } elseif (in_array($info, $mines) && !$not) {
            $this->addReturn('', 'not');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output           = parent::messages();
        $output[ 'must' ] = 'La valeur :label n\'est pas dans la liste.';
        $output[ 'not' ]  = 'La valeur de :label ne doit pas être dans la liste.';

        return $output;
    }
}
