<?php

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
class FileMimetypes extends File
{
    /**
     * Test si un fichier est d'un type de mime(application, image, font, text, video...)
     *
     * @param string                $key   Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param string                $arg   Mime exacte ou le type du mime du fichier.
     * @param bool                  $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        parent::test('file', $value, '', true);

        if ($this->hasErrors()) {
            return;
        }

        $mtype = $this->getMime($value);

        if (!(strpos($mtype, $arg) === 0) && $not) {
            $this->addReturn($key, 'mime_types', [ ':list' => $arg ]);
        } elseif (strpos($mtype, $arg) === 0 && !$not) {
            $this->addReturn($key, 'not_mime_types', [ ':list' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output                     = parent::messages();
        $output[ 'mime_types' ]     = 'The :label field must be a file of type :list.';
        $output[ 'not_mime_types' ] = 'The :label field must not be a file of type :list.';

        return $output;
    }
}
