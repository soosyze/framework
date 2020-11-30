<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Psr\Http\Message\UploadedFileInterface;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class FileExtensions extends File
{
    /**
     * Test si un fichier possède l'une des extensions fournit dans les arguments.
     *
     * @param string                $key   Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param string                $arg   Liste d'extensions séparé par une virgule.
     * @param bool                  $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        parent::test('file', $value, '', true);

        if ($this->hasErrors()) {
            return;
        }

        $extensions = explode(',', $arg);
        $extension  = $this->getExtension($value);

        if (!in_array($extension, $extensions) && $not) {
            $this->addReturn($key, 'ext', [ ':list' => $arg ]);
        } elseif (in_array($extension, $extensions) && !$not) {
            $this->addReturn($key, 'not_ext', [ ':list' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output              = parent::messages();
        $output[ 'ext' ]     = 'The :label field must be a file of type :list.';
        $output[ 'not_ext' ] = 'The :label field must not be a file of type :list.';

        return $output;
    }
}
