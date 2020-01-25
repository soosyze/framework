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
class FileMimetypes extends File
{
    /**
     * Test si un fichier est d'un type de mime(application, image, font, text, video...)
     *
     * @param string                $key   Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param string                $arg   Mime exacte ou le type du mime du fichier.
     * @param bool                  $not   Inverse le test.
     *
     * @return int 1 erreur de fichier.
     */
    protected function test($key, $value, $arg, $not)
    {
        parent::test('file', $value, false, true);

        if ($this->hasErrors()) {
            return 1;
        }

        $mtype = $this->getMime($value);

        if (!(strpos($mtype, $arg) === 0) && $not) {
            $this->addReturn($key, 'mime_types');
        } elseif (strpos($mtype, $arg) === 0 && !$not) {
            $this->addReturn($key, 'not_mime_types');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output                     = parent::messages();
        $output[ 'mime_types' ]     = 'La valeur :label n\'est pas dans la liste.';
        $output[ 'not_mime_types' ] = 'La valeur de :label ne doit pas être dans la liste.';

        return $output;
    }
}
